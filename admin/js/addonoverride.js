jQuery(document).ready(function () {

    jQuery(document).on('click', '#btn-override-files', function () {
        var addonId = jQuery(this).data('id');
        jQuery('#overrideModal').data('addon-id', addonId);
        jshopOverrideLoad(addonId, '', 0, true);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('overrideModal')).show();
    });

    jQuery(document).on('change', '#override-template-select', function () {
        var addonId = jQuery('#overrideModal').data('addon-id');
        var templateName = jQuery(this).val();
        var templateId = parseInt(jQuery('option:selected', this).data('id')) || 0;
        jshopOverrideLoad(addonId, templateName, templateId, false);
    });

    jQuery(document).on('click', '.js-override-one', function () {
        var btn = jQuery(this);
        var alias = btn.data('alias'), type = btn.data('type'), fileName = btn.data('file');
        var templateName = jQuery('#override-template-select').val();
        btn.prop('disabled', true);
        jQuery.ajax({
            url: 'index.php?option=com_jshopping&controller=addons&task=overridesave',
            type: 'POST', dataType: 'json',
            data: jQuery.extend({ alias: alias, type: type, file_name: fileName, template_name: templateName, folder: jshopOverrideFolder(type) }, jshopCsrfToken()),
            success: function (resp) {
                btn.prop('disabled', false);
                if (!resp.success) { alert(resp.message); return; }
                jshopOverrideLoad(jQuery('#overrideModal').data('addon-id'), templateName, parseInt(jQuery('#override-template-select option:selected').data('id')) || 0, false);
            },
            error: function () { btn.prop('disabled', false); }
        });
    });

    jQuery(document).on('click', '.js-override-all', function () {
        var btn = jQuery(this);
        var alias = btn.data('alias'), type = btn.data('type');
        var templateName = jQuery('#override-template-select').val();
        btn.prop('disabled', true);
        jQuery.ajax({
            url: 'index.php?option=com_jshopping&controller=addons&task=overridesave',
            type: 'POST', dataType: 'json',
            data: jQuery.extend({ alias: alias, type: type, file_name: '', template_name: templateName, folder: jshopOverrideFolder(type) }, jshopCsrfToken()),
            success: function (resp) {
                btn.prop('disabled', false);
                if (!resp.success) { alert(resp.message); return; }
                jshopOverrideLoad(jQuery('#overrideModal').data('addon-id'), templateName, parseInt(jQuery('#override-template-select option:selected').data('id')) || 0, false);
            },
            error: function () { btn.prop('disabled', false); }
        });
    });

    jQuery(document).on('click', '.js-override-delete', function () {
        var btn = jQuery(this);
        if (!confirm('Delete this override file?')) return;
        var alias = btn.data('alias'), type = btn.data('type'), fileName = btn.data('file');
        var templateName = jQuery('#override-template-select').val();
        btn.prop('disabled', true);
        jQuery.ajax({
            url: 'index.php?option=com_jshopping&controller=addons&task=overridedelete',
            type: 'POST', dataType: 'json',
            data: { alias: alias, type: type, file_name: fileName, template_name: templateName, folder: jshopOverrideFolder(type) },
            success: function (resp) {
                btn.prop('disabled', false);
                if (!resp.success) { alert(resp.message); return; }
                jshopOverrideLoad(jQuery('#overrideModal').data('addon-id'), templateName, parseInt(jQuery('#override-template-select option:selected').data('id')) || 0, false);
            },
            error: function () { btn.prop('disabled', false); }
        });
    });

    (function () {
        var btn = document.getElementById('btn-override-files');
        if (!btn) return;
        var names = ['config[folder_overrides_view]', 'config[folder_overrides_js]', 'config[folder_overrides_css]'];
        var orig = {};
        names.forEach(function (n) {
            var el = document.querySelector('input[name="' + n + '"]');
            if (el) orig[n] = el.value;
        });
        function check() {
            var dirty = names.some(function (n) {
                var el = document.querySelector('input[name="' + n + '"]');
                return el && el.value !== orig[n];
            });
            btn.disabled = dirty;
            btn.title = dirty ? Joomla.Text._('JSHOP_SAVE_FIRST') : '';
        }
        names.forEach(function (n) {
            var el = document.querySelector('input[name="' + n + '"]');
            if (el) el.addEventListener('input', check);
        });
    }());

});

function jshopOverrideTpl(id) {
    return document.getElementById(id).content.cloneNode(true);
}

function jshopOverrideSetLoading(full) {
    if (full) {
        var src = document.getElementById('override-sources');
        src.innerHTML = '';
        src.appendChild(jshopOverrideTpl('tpl-ovr-loading'));
    }
    var files = document.getElementById('override-files');
    files.innerHTML = '';
    files.appendChild(jshopOverrideTpl('tpl-ovr-loading'));
}

function jshopOverrideSetError(message) {
    var frag = jshopOverrideTpl('tpl-ovr-error');
    frag.querySelector('[data-error-msg]').textContent = message;
    var files = document.getElementById('override-files');
    files.innerHTML = '';
    files.appendChild(frag);
}

function jshopOverrideLoad(addonId, templateName, templateId, full) {
    jshopOverrideSetLoading(full);
    jQuery.ajax({
        url: 'index.php?option=com_jshopping&controller=addons&task=override',
        type: 'GET', dataType: 'json',
        data: { id: addonId, template_name: templateName, template_id: templateId },
        success: function (resp) {
            if (!resp.success) { jshopOverrideSetError(resp.message); return; }
            var d = resp.data;
            if (full) {
                var select = document.getElementById('override-template-select');
                select.innerHTML = '';
                d.templates.forEach(function (t) {
                    var opt = new Option(t.title, t.template, false, t.template === d.selected_template_name);
                    opt.dataset.id = t.id;
                    select.add(opt);
                });
                jshopOverrideRenderSources(d);
            }
            jshopOverrideRenderFiles(d);
        },
        error: function () { jshopOverrideSetError('Error loading data.'); }
    });
}

function jshopOverrideBuildSections(files, alias, paths, rowBuilder) {
    var sectionIcon = { view: 'icon-folder', js: 'icon-folder', css: 'icon-folder' };
    var sections = [['View', files.view, 'view'], ['JS', files.js, 'js'], ['CSS', files.css, 'css']];
    var result = document.createDocumentFragment();

    sections.forEach(function (item) {
        var title = item[0], sectionFiles = item[1], type = item[2];
        if (!sectionFiles.length) return;

        var frag = jshopOverrideTpl('tpl-ovr-section');
        frag.querySelector('[data-section-icon]').className = sectionIcon[type] + ' icon-fw';
        frag.querySelector('[data-section-title]').textContent = title;
        var pathEl = frag.querySelector('[data-section-path]');
        var path = paths && paths[type] ? paths[type] : '';
        if (path) {
            pathEl.textContent = path;
            pathEl.removeAttribute('hidden');
        }
        var sectionEl = frag.querySelector('[data-section]');
        var btnAllSlot = frag.querySelector('[data-section-btn-all]');

        rowBuilder(sectionEl, sectionFiles, type, alias, btnAllSlot);
        result.appendChild(sectionEl);
    });

    return result;
}

function jshopOverrideRenderSources(d) {
    var container = document.getElementById('override-sources');
    var frag = jshopOverrideBuildSections(d.sources, d.alias, d.paths && d.paths.source, function (sectionEl, files, type, alias, btnAllSlot) {
        if (files.length > 1 && btnAllSlot) {
            var btnFrag = jshopOverrideTpl('tpl-ovr-btn-all');
            var btnEl = btnFrag.querySelector('.js-override-all');
            btnEl.dataset.type = type;
            btnEl.dataset.alias = alias;
            btnAllSlot.appendChild(btnFrag);
        }
        files.forEach(function (f) {
            var rowFrag = jshopOverrideTpl('tpl-ovr-src-row');
            rowFrag.querySelector('[data-fname]').textContent = f;
            var rowBtn = rowFrag.querySelector('.js-override-one');
            rowBtn.dataset.type = type;
            rowBtn.dataset.alias = alias;
            rowBtn.dataset.file = f;
            sectionEl.appendChild(rowFrag);
        });
    });

    container.innerHTML = '';
    if (frag.childNodes.length) {
        container.appendChild(frag);
    } else {
        container.appendChild(jshopOverrideTpl('tpl-ovr-src-empty'));
    }
}

function jshopOverrideRenderFiles(d) {
    var container = document.getElementById('override-files');
    var frag = jshopOverrideBuildSections(d.overrides, d.alias, d.paths && d.paths.override, function (sectionEl, files) {
        files.forEach(function (f) {
            var rowFrag = jshopOverrideTpl('tpl-ovr-file-row');
            rowFrag.querySelector('[data-fname]').textContent = f.name;
            if (f.edit_url) {
                var link = rowFrag.querySelector('[data-edit-link]');
                link.href = f.edit_url;
                link.removeAttribute('hidden');
            }
            sectionEl.appendChild(rowFrag);
        });
    });

    container.innerHTML = '';
    if (frag.childNodes.length) {
        container.appendChild(frag);
    } else {
        container.appendChild(jshopOverrideTpl('tpl-ovr-files-empty'));
    }
}

function jshopOverrideFolder(type) {
    var map = { view: 'folder_overrides_view', js: 'folder_overrides_js', css: 'folder_overrides_css' };
    return jQuery('input[name="config[' + map[type] + ']"]').val() || '';
}

function jshopCsrfToken() {
    var token = Joomla.getOptions('csrf.token');
    var d = {};
    d[token] = 1;
    return d;
}
