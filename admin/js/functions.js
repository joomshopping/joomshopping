var jshopAdminClass = function(){

    var that = this;

    this.jstriggers = {
        addAttributValueHtml: '',
        addAttributValue2Html: '',
        addAttributValueEvents: [],
        addAttributValue2Events: []
    };

    this.Round = function(value, numCount){
        var ret = parseFloat(Math.round(value * Math.pow(10, numCount)) / Math.pow(10, numCount)).toString();
        return (isNaN(ret)) ? (0) : (ret);
    };
	
	this.floatVal = function(val) {
        if (!val) val = '';
		let res = parseFloat(val.replace(',', '.'));
		if (isNaN(res)) res = 0;
		return res;
	}

    this.changeCategory = function(){
        var catid = jQuery("#category_parent_id").val();
        var url = 'index.php?option=com_jshopping&controller=categories&task=sorting_cats_html&catid='+catid+"&ajax=1";        
        jQuery.get(url, function(data){
            jQuery('#ordering').html(data);
        });
    };

    this.verifyStatus = function(orderStatus, orderId, message, extended){        
        if (extended == 0){
            var statusNewId = $('#select_status_id' + orderId).val();
            if (statusNewId == orderStatus){
                alert (message);
                return;
            } else {
                var isChecked = (jQuery('#order_check_id_' + orderId ).prop('checked')) ? 1 : 0;
                jQuery('#js_nolang').val(1);
                jQuery('form#adminForm').append('<input type="hidden" name="order_id" value="'+orderId+'">');
                jQuery('form#adminForm').append('<input type="hidden" name="order_status" value="'+statusNewId+'">');
                jQuery('form#adminForm').append('<input type="hidden" name="notify" value="'+isChecked+'">');
                Joomla.submitbutton('update_status');
            }
        } else {
            var statusNewId = jQuery('#order_status').val();
            if (statusNewId == orderStatus){
                  alert (message);
                  return;
            } else {
                jQuery('#js_nolang').val(1);
                Joomla.submitbutton('update_one_status');
            }
        }
    };

    this.updatePrice = function(display_price_admin){
        var repl = new RegExp("\,", "i");        
        var percent = $("#product_tax_id option:selected").text();
        var pattern = /(\d*\.?\d*)%\)$/
        pattern.test(percent);
        percent = RegExp.$1;
        var price2 = this.floatVal($('#product_price2').val());
        if (display_price_admin==0){
            $('#product_price').val(this.Round(price2 * (1 + percent / 100), this.product_price_precision));
        }else{
            $('#product_price').val(this.Round(price2 / (1 + percent / 100), this.product_price_precision));
        }
        this.reloadAddPriceValue();
    };

    this.updatePrice2 = function(display_price_admin){
        var repl = new RegExp("\,", "i");
        var percent = $("#product_tax_id option:selected").text();
        var pattern = /(\d*\.?\d*)%\)$/
        pattern.test(percent);
        percent = RegExp.$1;
        var price = this.floatVal($('#product_price').val());
        if (display_price_admin==0){
            $('#product_price2').val(this.Round(price / (1 + percent / 100), this.product_price_precision));
        }else{
            $('#product_price2').val(this.Round(price * (1 + percent / 100), this.product_price_precision));
        }
        this.reloadAddPriceValue();
    };

    this.addNewPrice = function(){
        this.add_price_num++;
        var html;
        html = '<tr id="add_price_'+this.add_price_num+'">';
        html += '<td><input type = "text" class="form-control small3" name = "quantity_start[]" id="quantity_start_'+this.add_price_num+'" value = "" /></td>';
        html += '<td><input type = "text" class="form-control small3" name = "quantity_finish[]" id="quantity_finish_'+this.add_price_num+'" value = "" /></td>';
        html += '<td><input type = "text" class="form-control small3" name = "product_add_discount[]" id="product_add_discount_'+this.add_price_num+'" value = "" onkeyup="jshopAdmin.productAddPriceupdateValue('+this.add_price_num+')" /></td>';
        html += '<td><input type = "text" class="form-control small3" id="product_add_price_'+this.add_price_num+'" value = "" onkeyup="jshopAdmin.productAddPriceupdateDiscount('+this.add_price_num+')" /></td>';
        html += '<td align="center"><a href="#" class="btn btn-danger" onclick="jshopAdmin.delete_add_price('+this.add_price_num+');return false;"><i class="icon-delete"></i></a></td>';
        html += '</tr>';
        jQuery("#table_add_price").append(html);
    };

    this.delete_add_price = function(num){
        jQuery("#add_price_"+num).remove();
    };

    this.productAddPriceupdateValue = function(num){
        var price;
        var origin = this.floatVal(jQuery("#product_price").val());
        if (origin=="") return 0;
        var discount = this.floatVal(jQuery("#product_add_discount_"+num).val());
        if (discount=="") return 0;
        if (this.config_product_price_qty_discount==1)
            price = origin - discount;
        else
            price = origin - (origin * discount/100);
        jQuery("#product_add_price_"+num).val(price);
    };

    this.productAddPriceupdateDiscount = function(num){
        var price;
        var origin = this.floatVal(jQuery("#product_price").val());
        if (origin=="") return 0;
        var price = this.floatVal(jQuery("#product_add_price_"+num).val());
        if (price=="") return 0;
        if (this.config_product_price_qty_discount==1)
            discount = origin - price;
        else
            discount = 100 - (price / origin * 100);
        jQuery("#product_add_discount_"+num).val(discount);
    };

    this.reloadAddPriceValue = function(){
        var discount;
        var origin = this.floatVal(jQuery("#product_price").val());
        jQuery("#attr_price").val(origin);

        if (origin=="") return 0;
        for(i=0;i<=this.add_price_num;i++){
            if (jQuery("#product_add_discount_"+i)){
                discount = jQuery("#product_add_discount_"+i).val();
                if (this.config_product_price_qty_discount==1)
                    price = origin - discount;
                else
                    price = origin - (origin * discount/100);
                jQuery("#product_add_price_"+i).val(price);
            }
        }
    };

    this.updateEanForAttrib = function(){
        jQuery("#attr_ean").val(jQuery("#product_ean").val());
    };

    this.addFieldShPrice = function(){
        this.shipping_weight_price_num++;
        var html;
        html = '<tr id="shipping_weight_price_row_'+this.shipping_weight_price_num+'">';
        html += '<td><input type = "text" class = "inputbox form-control" name = "shipping_weight_from[]" value = "" /></td>';
        html += '<td><input type = "text" class = "inputbox form-control" name = "shipping_weight_to[]" value = "" /></td>';
        html += '<td><input type = "text" class = "inputbox form-control" name = "shipping_price[]" value = "" /></td>';
        html += '<td><input type = "text" class = "inputbox form-control" name = "shipping_package_price[]" value = "" /></td>';
        html += '<td style="text-align:center"><a class="btn btn-danger" href="#" onclick="jshopAdmin.delete_shipping_weight_price_row('+this.shipping_weight_price_num+');return false;"><i class="icon-delete"></i></a></td>';
        html += '</tr>';
        jQuery("#table_shipping_weight_price").append(html);
    };

    this.delete_shipping_weight_price_row = function(num){
        jQuery("#shipping_weight_price_row_"+num).remove();
    };

    this.setDefaultSize = function(width, height, param){
       jQuery('#'+param + '_width_image').val(width);
       jQuery('#'+param + '_height_image').val(height);
       jQuery('#'+param + '_width_image').prop("disabled", true);
       jQuery('#'+param + '_height_image').prop("disabled", true);
    };

    this.setOriginalSize = function(param){
       jQuery('#'+param + '_width_image').prop("disabled", true);
       jQuery('#'+param + '_height_image').prop("disabled", true);
       jQuery('#'+param + '_width_image').val(0);
       jQuery('#'+param + '_height_image').val(0);
    };

    this.setManualSize = function(param){
       jQuery('#'+param + '_width_image').prop("disabled", false);
       jQuery('#'+param + '_height_image').prop("disabled", false);
    };

    this.setFullOriginalSize = function(param){
       jQuery('#'+param + '_width_image').prop("disabled", true);
       jQuery('#'+param + '_height_image').prop("disabled", true);
       jQuery('#'+param + '_width_image').val(0);
       jQuery('#'+param + '_height_image').val(0);
    };

    this.setFullManualSize = function(param){
       jQuery('#'+param + '_width_image').prop("disabled", false);
       jQuery('#'+param + '_height_image').prop("disabled", false);
    };

    this.addAttributValue2 = function(id){
        var value_id = jQuery("#attr_ind_id_tmp_"+id+"  :selected").val();
        var attr_value_text = jQuery("#attr_ind_id_tmp_"+id+"  :selected").text();
        var mod_price = jQuery("#attr_price_mod_tmp_"+id).val();
        var price = jQuery("#attr_ind_price_tmp_"+id).val();
        var existcheck = jQuery('#attr_ind_'+id+'_'+value_id).val();
        if (existcheck){
            alert(this.lang_attribute_exist);
            return 0;
        }
        if (value_id=="0"){
            alert(this.lang_error_attribute);
            return 0;
        }
        html = "<tbody><tr id='attr_ind_row_"+id+"_"+value_id+"'>";
        hidden = "<input type='hidden' id='attr_ind_"+id+"_"+value_id+"' name='attrib_ind_id[]' value='"+id+"'>";
        hidden2 = "<input type='hidden' name='attrib_ind_value_id[]' value='"+value_id+"'>";
        tmpimg="";
        if (value_id!=0 && this.attrib_images[value_id]!=""){
            tmpimg ='<img src="'+this.folder_image_attrib+'/'+this.attrib_images[value_id]+'" style="margin-right:5px;" width="16" height="16" class="img_attrib">';
        }
        html+="<td>" + hidden + hidden2 + tmpimg + attr_value_text + "</td>";
        html+="<td><input type='text' class='small3 form-control' name='attrib_ind_price_mod[]' value='"+mod_price+"'></td>";
        html+="<td><input type='text' class='small3 form-control' name='attrib_ind_price[]' value='"+price+"'></td>";
        html+=this.jstriggers.addAttributValue2Html;
        html+="<td><a class='btn btn-danger' href='#' onclick=\"jQuery('#attr_ind_row_"+id+"_"+value_id+"').remove();return false;\"><i class=\"icon-delete\"></i></a></td>";
        html += "</tr></tbody>";
        jQuery("#list_attr_value_ind_"+id).append(html);
        jQuery.each(this.jstriggers.addAttributValue2Events, function(key, handler){
            handler.call(this, id);
        });
    };

    this.addAttributValue = function(){
        this.attr_tmp_row_num++;
        var id=0;
        var ide=0;
        var value = "";
        var text = "";
        var html="";
        var hidden="";
        var field="";
        var count_attr_sel = 0;
        var tmpmass = {};
        var tmpimg = "";
        var selectedval = {};
        var num = 0;
        var current_index_list = [];
        var max_index_list = [];
        var combination = 1;
        var count_attributs = this.attrib_ids.length;
        var index = 0;
        var option = {};
        jshopAdmin.ajaxLoadAnimate().show();

        for (var i=0; i<count_attributs; i++){
            current_index_list[i] = 0;
            id = this.attrib_ids[i];
            ide = "value_id"+id;
            selectedval[id] = [];
            num = 0;
            jQuery("#"+ide+" :selected").each(function(j, selected){
                value = jQuery(selected).val();
                text = jQuery(selected).text();
                if (value!=0){
                    selectedval[id][num] = {"text":text, "value":value};
                    num++;
                }
            });

            if (selectedval[id].length==0){
                selectedval[id][0] = {"text":"-", "value":"0"};
            }else{
                count_attr_sel++;
            }
            max_index_list[i] = selectedval[id].length;
            combination = combination * max_index_list[i];
        }

        var first_attr = [];
        jQuery('input:hidden[name^="attrib_id"]',"#list_attr_value tr:eq(1)").each(function(index, element){
            var VRegExp = new RegExp(/attrib_id\[(\d+)\]\[\d*\]/);
            var attr_id = VRegExp.exec(jQuery(this).attr('name'))[1];
            first_attr[attr_id] = jQuery(this).val();
        });
        if (first_attr.length > 0) {
            let active_attr_names = [];
            for(let k in first_attr) {
                active_attr_names.push(this.attrib_names[k]);
            }
            let active_attr_names_text = active_attr_names.join(', ');
            for (var k=0; k<count_attributs; k++){
                id = this.attrib_ids[k];
                if (first_attr[id] !== undefined){
                    if (first_attr[id]==0 && selectedval[id][0].value != 0){
                        jshopAdmin.ajaxLoadAnimate().hide();
                        alert(this.lang_error_attribute+' ('+active_attr_names_text+')');
                        return 0;
                    }
                    if (first_attr[id]!=0 && selectedval[id][0].value == 0){
                        jshopAdmin.ajaxLoadAnimate().hide();
                        alert(this.lang_error_attribute+' ('+active_attr_names_text+')');
                        return 0;
                    }
                } else {
                    if (selectedval[id][0].value != 0) {
                        jshopAdmin.ajaxLoadAnimate().hide();
                        alert(this.lang_error_attribute+' ('+active_attr_names_text+')');
                        return 0;
                    }
                }
            }
        } else {
            jQuery('#list_attr_value thead tr th.atr').remove();
            let htmlatr = '';
            for (var k=0; k<count_attributs; k++){
                id = this.attrib_ids[k];
                if (selectedval[id][0].value != 0) {
                    htmlatr += '<th class="atr">'+this.attrib_names[id]+'</th>';
                }
            }
            jQuery('#list_attr_value thead tr').prepend(htmlatr);
        }

        if (count_attr_sel==0){
            jshopAdmin.ajaxLoadAnimate().hide();
            alert(this.lang_error_attribute);
            return 0;
        }

        var list_key = [];
        for(var j=0; j<combination; j++){
            list_key[j] = [];
            for (var i=0; i<count_attributs; i++){
                id = this.attrib_ids[i];
                num = current_index_list[i];
                list_key[j][i] = num;
            }

            index = 0;
            for (var i=0; i<count_attributs; i++){
                if (i==index){
                    current_index_list[index]++;
                    if (current_index_list[index] >= max_index_list[index]){
                        current_index_list[index] = 0;
                        index++;
                    }
                }
            }
        }

        var entered_price = jQuery("#attr_price").val();
        var entered_count = jQuery("#attr_count").val();
        var entered_ean = jQuery("#attr_ean").val();
        var entered_manufacturer_code = jQuery("#attr_manufacturer_code").val();
        var entered_real_ean = jQuery("#attr_real_ean").val();
        var entered_weight = jQuery("#attr_weight").val();
        var entered_weight_volume_units = jQuery("#attr_weight_volume_units").val();
        var entered_old_price = jQuery("#attr_old_price").val();
        var entered_buy_price = jQuery("#attr_buy_price").val();
        var count_added_rows = 0;
        for(var j=0; j<combination; j++){
            tmpmass = {};
            html = "<tr id='attr_row_"+this.attr_tmp_row_num+"'>";
            for (var i=0; i < count_attributs; i++){
                id = this.attrib_ids[i];
                num = list_key[j][i];
                option = selectedval[id][num];
                if (option.value != 0) {
                    hidden = "<input type='hidden' name='attrib_id["+id+"][]' value='"+option.value+"'>";
                    tmpimg="";
                    if (option.value!=0 && this.attrib_images[option.value]!=""){
                        tmpimg ='<img src="'+this.folder_image_attrib+'/'+this.attrib_images[option.value]+'" style="margin-right:5px;" width="16" height="16" class="img_attrib">';
                    }
                    html+="<td class='atr'>" + hidden + tmpimg + option.text + "</td>";
                }
                tmpmass[id] = option.value;
            }

            field="<input class='form-control' type='text' name='attrib_price[]' value='"+entered_price+"'>";
            html+="<td>"+field+"</td>";

            html+=this.jstriggers.addAttributValueHtml;

            if (this.use_stock=="1"){
                field="<input class='form-control' type='text' name='attr_count[]' value='"+entered_count+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_product_ean=="1"){
                field="<input class='form-control' type='text' name='attr_ean[]' value='"+entered_ean+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_manufacturer_code=="1"){
                field="<input class='form-control' type='text' name='attr_manufacturer_code[]' value='"+entered_manufacturer_code+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_real_ean=="1"){
                field="<input class='form-control' type='text' name='attr_real_ean[]' value='"+entered_real_ean+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_weight=="1"){
                field="<input class='form-control' type='text' name='attr_weight[]' value='"+entered_weight+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_basic_price=="1"){
                field="<input class='form-control' type='text' name='attr_weight_volume_units[]' value='"+entered_weight_volume_units+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_product_old_price=="1"){
                field="<input class='form-control' type='text' name='attrib_old_price[]' value='"+entered_old_price+"'>";
                html+="<td>"+field+"</td>";
            }

            if (this.use_bay_price=="1"){
                field="<input class='form-control' type='text' name='attrib_buy_price[]' value='"+entered_buy_price+"'>";
                html+="<td>"+field+"</td>";
            }

            html+="<td></td><td class='center'><input type='hidden' name='product_attr_id[]' value='0'><input type='checkbox' class='ch_attr_delete' value='"+this.attr_tmp_row_num+"'></td>";

            html+="</tr>";

            var existcheck = 0;
            for ( var k in this.attrib_exist ){
                var exist = 1;
                for(var i=0; i<count_attributs; i++){
                    id = this.attrib_ids[i];
                    if (this.attrib_exist[k][id]!=tmpmass[id]) exist=0;
                }
                if (exist==1) {
                    existcheck = 1;
                    break;
                }
            }

            if (!existcheck){
                jQuery("table#list_attr_value tbody").append(html);
                this.attrib_exist[this.attr_tmp_row_num] = tmpmass;
                this.attr_tmp_row_num++;
                count_added_rows++;
            }
        }

        if (count_added_rows==0){
            jshopAdmin.ajaxLoadAnimate().hide();
            alert(this.lang_attribute_exist);
            return 0;
        }
        jQuery.each(this.jstriggers.addAttributValueEvents, function(key, handler){
            handler.call(this, count_added_rows);
        });
        jshopAdmin.ajaxLoadAnimate().hide();
        return 1;
    };

    this.deleteTmpRowAttrib = function(num){
        jQuery("#attr_row_"+num).remove();
        delete this.attrib_exist[num];
    };

    this.selectAllListAttr = function(checked){
        jQuery(".ch_attr_delete").prop('checked', checked);
    };

    this.deleteListAttr = function(){
        jQuery("#ch_attr_delete_all").prop('checked', false);
        jQuery(".ch_attr_delete").each(function(i){
            if (jQuery(this).is(':checked')){
                that.deleteTmpRowAttrib(jQuery(this).val());
            }
        });
    };

    this.deleteFotoCategory = function(catid){
        var url = 'index.php?option=com_jshopping&controller=categories&task=delete_foto&catid='+catid;
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#foto_category").hide();
        });
    }

    this.deleteFotoProduct = function(id){
        var url = 'index.php?option=com_jshopping&controller=products&task=delete_foto&id='+id; 
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#foto_product_"+id).hide();
        });
    };

    this.deleteVideoProduct = function(id){
        var url = 'index.php?option=com_jshopping&controller=products&task=delete_video&id='+id;
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#video_product_"+id).hide();
        });
    };

    this.deleteFileProduct = function(id, type){
        var url = 'index.php?option=com_jshopping&controller=products&task=delete_file&id='+id+"&type="+type;
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            if (type=="demo"){
                jQuery("#product_demo_"+id).html("");
            }
            if (type=="file"){
                jQuery("#product_file_"+id).html("");
            }
            if (data=="1") jQuery(".rows_file_prod_"+id).hide();
        });
    };

    this.deleteFotoManufacturer = function(id){
        var url = 'index.php?option=com_jshopping&controller=manufacturers&task=delete_foto&id='+id;
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#image_manufacturer").hide();
        });
    };

    this.deleteFotoAttribValue = function(id){
        var url = 'index.php?option=com_jshopping&controller=attributesvalues&task=delete_foto&id='+id;
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#image_attrib_value").hide();
        });
    };

    this.deleteFotoLabel = function(id){
        var url = 'index.php?option=com_jshopping&controller=productlabels&task=delete_foto&id='+id;
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#image_block").hide();
        });
    };

    this.releted_product_search = function(start, no_id, page){
        var text = jQuery("#related_search").val();
        var url = 'index.php?option=com_jshopping&controller=products&task=search_related&start='+start+'&no_id='+no_id+'&text='+encodeURIComponent(text)+"&ajax=1";
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, function(data){
            jQuery("#list_for_select_related").html(data);
            jQuery(".pagination.related .page a").removeClass('active');
            jQuery(".pagination.related .page a.p"+page).addClass('active');
        });
    };

    this.add_to_list_relatad = function(id){
        var name = jQuery("#serched_product_"+id+" .name").html();
        var img =  jQuery("#serched_product_"+id+" .image").html();
        var html = '<div class="block_related" id="related_product_'+id+'">';
        html += '<div class="block_related_inner">';
        html += '<div class="name">'+name+'</div>';
        html += '<div class="image">'+img+'</div>';
        html += '<div style="padding-top:5px;"><input type="button" class="btn btn-danger btn-small" value="'+this.lang_delete+'" onclick="jshopAdmin.delete_related('+id+')"></div>';
        html += '<input type="hidden" name="related_products[]" value="'+id+'"/>';
        html += '</div>';
        html += '</div>';
        jQuery("#list_related").append(html);
    };

    this.delete_related = function(id){
        jQuery("#related_product_"+id).remove();
    };

    this.reloadProductExtraField = function(product_id, edittype){
        var catsurl = "";
		if (!edittype) edittype = '';
        jQuery("#category_id :selected").each(function(j, selected){
            value = jQuery(selected).val();
            text = jQuery(selected).text();
            if (value!=0){
                catsurl += "&cat_id[]="+value;
            }
        });

        var url = 'index.php?option=com_jshopping&controller=products&task=product_extra_fields_hide&product_id='+product_id+catsurl+"&ajax=1&edittype="+edittype;
        jQuery.ajaxSetup({ cache: false });
        jQuery.getJSON(url, function(json){
            for (let item of json) {
                if (item.row_class == 'hide') {
                    jQuery('.list_prod_extrafields tr[extrafieldid='+item.id+']').addClass('hide');
                } else {
                    jQuery('.list_prod_extrafields tr[extrafieldid='+item.id+']').removeClass('hide');
                }
            }
            jshopAdmin.reloadDisplayProductExtraGroup();
        });
    };

    this.reloadDisplayProductExtraGroup = function() {
        jQuery('.list_prod_extrafields .ef_group').each(function(){
            let id = jQuery(this).attr('ef_gr_id');
            let hide = 1;
            jQuery('.list_prod_extrafields tr[extrafield_gr_id='+id+']').each(function(){
                if (!jQuery(this).hasClass('hide')) {
                    hide = 0;
                }
            });
            if (hide) {
                jQuery(this).addClass('hide');
            } else {
                jQuery(this).removeClass('hide');
            }
        })
    }

    this.PFShowHideSelectCats = function(){
        var value = jQuery("input[name=allcats]:checked").val();
        if (value=="0"){
            jQuery("#tr_categorys").show();
        }else{
            jQuery("#tr_categorys").hide();
        }
    };

    this.ShowHideEnterProdQty = function(checked){
        if (checked){
            jQuery("#block_enter_prod_qty").hide();
        }else{
            jQuery("#block_enter_prod_qty").show();
        }
    };

    this.editAttributeExtendParams = function(id){
        prod_attr_poup = window.open('index.php?option=com_jshopping&controller=products&task=edit&product_attr_id='+id,'windowae','width=1000, height=760, scrollbars=yes,status=no,toolbar=no,menubar=no,resizable=yes,location=yes');
    };

    this.addOrderItemRow = function(){
        this.end_number_order_item++;
        var i = this.end_number_order_item;
        var html = '<tbody><tr valign="top" id="order_item_row_'+i+'">';
        html+='<td><input type="text" name="product_name['+i+']" class="form-control mb-2" value="" size="44" />';
        html+='<a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aModal" onclick="jshopAdmin.cElName='+i+'">'+this.lang_load+'</a><br>';
        if (this.admin_show_manufacturer_code){
            html+='<input type="text"  class="form-control" name="manufacturer_code['+i+']"><br>';
        }
        if (this.admin_show_real_ean){
            html+='<input type="text"  class="form-control" name="real_ean['+i+']"><br>';
        }
        if (this.admin_show_attributes){
            html+='<textarea rows="2" cols="24" name="product_attributes['+i+']"  class="form-control"></textarea><br />';
        }
        if (this.admin_show_freeattributes){
            html+='<textarea rows="2" cols="24" name="product_freeattributes['+i+']"  class="form-control"></textarea><br />';
        }
        html+='<input type="hidden" name="product_id['+i+']" value="">';
        html+='<input type="hidden" name="delivery_times_id['+i+']" value="">';
        html+='<input type="hidden" name="thumb_image['+i+']" value="">';
        html+='<input type="hidden" name="attributes['+i+']" value="">';
        html+='<input type="hidden" name="category_id['+i+']" value="">';
        if (this.admin_order_edit_more){
            html+='<div>'+this.lang_weight+' <input type="text" name="weight['+i+']" value=""  class="form-control"></div>';
            html+='<div>'+this.lang_vendor+' ID <input type="text" name="vendor_id['+i+']" value=""  class="form-control"></div>';
        }else{
            html+='<input type="hidden" name="weight['+i+']" value="" >';
            html+='<input type="hidden" name="vendor_id['+i+']" value="" >';
        }
        html+='</td>';
        html+='<td><input type="text" name="product_ean['+i+']" class="middle form-control" value="" /></td>';
        html+='<td><input type="text" name="product_quantity['+i+']" class="small3 form-control" value="" onkeyup="jshopAdmin.updateOrderSubtotalValue();"/></td>';
        html+='<td width="20%">';
        html+='<div class="price d-flex align-items-center"><div class="small-title">'+this.lang_price+':</div><input type="text" class="small3 form-control" name="product_item_price['+i+']" value="" onkeyup="jshopAdmin.updateOrderSubtotalValue();"/></div>';
        if (!this.hide_tax){
        html+='<div class="tax d-flex align-items-center"><div class="small-title">'+this.lang_tax+':</div><input type="text" class="small3 form-control" name="product_tax['+i+']" value="" />%</div>';
        }
        html+='<input type="hidden" name="order_item_id['+i+']" value=""></td>';
        html+='<td><a class="btn btn-danger" href="#" onclick="jQuery(\'#order_item_row_'+i+'\').remove();jshopAdmin.updateOrderSubtotalValue();return false;"><i class="icon-delete"></i></a></td>';
        html+='</tr></tbody>';
        jQuery("#list_order_items").append(html);        
    };

    this.loadProductInfoRowOrderItem = function(pid, num, currency_id, display_price, user_id, load_attribute){
        var url = 'index.php?option=com_jshopping&controller=products&task=loadproductinfo&product_id='+pid+'&id_currency='+currency_id+'&ajax=1&display_price='+display_price;
        if (user_id>0){
            url+='&admin_load_user_id='+user_id;
        }
        if (typeof(load_attribute)==='undefined'){
            load_attribute = -1;
        }
        jQuery.getJSON(url, function(json){
            jQuery("input[name=product_id\\["+num+"\\]]").val(json.product_id);
            jQuery("input[name=category_id\\["+num+"\\]]").val(json.category_id);
            jQuery("input[name=product_name\\["+num+"\\]]").val(json.product_name);
            jQuery("input[name=product_ean\\["+num+"\\]]").val(json.product_ean);
            jQuery("input[name=manufacturer_code\\["+num+"\\]]").val(json.manufacturer_code);
            jQuery("input[name=real_ean\\["+num+"\\]]").val(json.real_ean);
            jQuery("input[name=product_item_price\\["+num+"\\]]").val(json.product_price);
            jQuery("input[name=product_tax\\["+num+"\\]]").val(json.product_tax);
            jQuery("input[name=weight\\["+num+"\\]]").val(json.product_weight);
            jQuery("input[name=delivery_times_id\\["+num+"\\]]").val(json.delivery_times_id);
            jQuery("input[name=vendor_id\\["+num+"\\]]").val(json.vendor_id);
            jQuery("input[name=thumb_image\\["+num+"\\]]").val(json.thumb_image);
            jQuery("input[name=product_quantity\\["+num+"\\]]").val(1);
            jQuery("textarea[name=product_attributes\\["+num+"\\]]").val('');
            jQuery("input[name=attributes\\["+num+"\\]]").val('');

            that.updateOrderSubtotalValue();
            if (load_attribute!=-1){
                if (load_attribute==1 && json.count_attributes>0){
                    url = "index.php?option=com_jshopping&controller=products&task=getattributes&tmpl=component&product_id="+pid+"&num="+num+'&id_currency='+currency_id+'&display_price='+display_price;
                    if (user_id>0){
                        url+='&admin_load_user_id='+user_id;
                    }
                    jQuery('#aModal .iframe').attr('src', url);
                }else{
                    window.parent.jQuery('#aModal').modal('hide');
                }
            }
        });
    };

    this.loadProductAttributeInfoOrderItem = function(num){
        jQuery("input[name=product_item_price\\["+num+"\\]]", window.parent.document).val( jQuery('#pricefloat').val() );
        jQuery("input[name=product_ean\\["+num+"\\]]", window.parent.document).val( jQuery('#product_code').html() );
        jQuery("input[name=weight\\["+num+"\\]]", window.parent.document).val( jQuery('#block_weight').html() );
        jQuery("input[name=manufacturer_code\\["+num+"\\]]", window.parent.document).val( jQuery('#manufacturer_code').html() );
        jQuery("input[name=real_ean\\["+num+"\\]]", window.parent.document).val( jQuery('#real_ean').html() );
        var attributetext = '';
        var attr = {};
        for(var i=0;i<jshopParams.attr_list.length;i++){
            var id = jshopParams.attr_list[i];
            attributetext += jQuery('#attr_name_id_'+id).html();
            attributetext += " ";
            attributetext += jQuery('#jshop_attr_id'+id+' option:selected').text();
            attributetext += "\n";
            attr[parseInt(id)] = parseInt(jQuery('#jshop_attr_id'+id).val());
        }
        jQuery("input[name=attributes\\["+num+"\\]]", window.parent.document).val(JSON.stringify(attr));
        jQuery("textarea[name=product_attributes\\["+num+"\\]]", window.parent.document).val(attributetext);

        window.parent.jshopAdmin.updateOrderSubtotalValue();        
        window.parent.jQuery('#aModal').modal('hide');
        
    };

    this.addOrderTaxRow = function(){
        var html="<tr class='tax-manual'>";
        html+='<td class="right"><input type="text" class="form-control small3" name="tax_percent[]"/> %</td>';
        html+='<td class="left"><input type="text" class="form-control small3" name="tax_value[]" onkeyup="jshopAdmin.updateOrderTotalValue();"/></td>';
        html+='</tr>';
        jQuery("#row_button_add_tax").before(html);
    };

    this.updateOrderSubtotalValue = function() {
        var result = 0;
        var regExp = /product_item_price\[(\d+)\]/i;
        jQuery("input[name^=product_item_price]").each(function(){
            var myArray = regExp.exec(jQuery(this).attr("name"));
            var value = myArray[1];
            var price = that.floatVal(jQuery(this).val());            
            var quantity = that.floatVal(jQuery("input[name=product_quantity\\["+value+"\\]]").val());            
            result += price * quantity;
        });

        jQuery("input[name=order_subtotal]").val(result);
        this.order_tax_calculate();
    };

    this.updateOrderTotalValue = function() {
        var result = 0;
        var subtotal = that.floatVal(jQuery("input[name=order_subtotal]").val());
        var discount = that.floatVal(jQuery("input[name=order_discount]").val());
        var shipping = that.floatVal(jQuery("input[name=order_shipping]").val());
        var opackage = that.floatVal(jQuery("input[name=order_package]").val());
        var payment = that.floatVal(jQuery("input[name=order_payment]").val());
        result = subtotal - discount + shipping+opackage + payment;

        if (jQuery("#display_price option:selected").val() == 1) {
            jQuery("input[name^=tax_value]").each(function(){
                var tax_value = that.floatVal(jQuery(this).val());
                result += tax_value;
            });
        }

        jQuery("input[name=order_total]").val(result);
    };

    this.changeVideoFileField = function(obj, type) {
        isChecked = jQuery(obj).is(':checked');
        var td_inputs = jQuery(obj).closest('td');
        td_inputs.find("textarea[name^='product_video_code_']").val('');
        td_inputs.find("textarea[name^='product_video_code_']").hide();
        td_inputs.find("input[name^='product_video_']").val('');
        td_inputs.find("input[name^='product_video_']").hide();
        td_inputs.find("input[name^='product_folder_video_']").val('');
        td_inputs.find(".product_folder_video").hide();
        if (isChecked) {
            if (type == 'code') {
                td_inputs.find("textarea[name^='product_video_code_']").show();
                td_inputs.find(".pvf_sel input[type=checkbox]").prop('checked', false);
            }
            if (type == 'folder') {
                td_inputs.find(".product_folder_video").show();
                td_inputs.find(".pvf_code input[type=checkbox]").prop('checked', false);
            }
        } else {
            td_inputs.find("input[name^='product_video_']").show();
        }
    };

    this.updateAllVideoFileField = function() {
        jQuery("table.admintable .pvf_code input[type=checkbox]").each(function(){
            that.changeVideoFileField(this, 'code');
        });
    };

    this.userEditenableFields = function(val){
        if (val==1){
            jQuery('.endes').removeAttr("disabled");
        }else{
            jQuery('.endes').attr('disabled','disabled');
        }
    };

    this.setBillingShippingFields = function(user){
        for(var field in user){
            jQuery(".jshop_address [name='" + field + "']").val(user[field]);
        }
    };

    this.updateBillingShippingForUser = function(user_id){
        if (user_id > 0) {
            var data = {};
            data['user_id'] = user_id;
            data['copy_empty_delivery_adress'] = '1';
            if (this.userinfo_ajax){
                this.userinfo_ajax.abort();
            }
            this.userinfo_ajax = jQuery.ajax({
                url: that.userinfo_link,
                dataType: "json",
                data: data,
                type: "post",
                success: function(json) {
                    that.setBillingShippingFields(json);
                }
            });
        } else {
            that.setBillingShippingFields(this.userinfo_fields);
        }
    };

    this.changeCouponType = function(){
        var val = jQuery("input[name=coupon_type]:checked").val();
        if (val==0){
            jQuery("#ctype_percent").show();
            jQuery("#ctype_value").hide();
        }else{
            jQuery("#ctype_percent").hide();
            jQuery("#ctype_value").show();
        }
    };

    this.setImageFromFolder = function(filename){
        jQuery("input[name='product_folder_image_" + this.cElName + "']").val(filename);
        jQuery('#aModal').modal('hide');
    };

    this.setVideoFromFolder = function(filename){
        jQuery("input[name='product_folder_video_" + this.cElName + "']").val(filename);
        jQuery('#videosModal').modal('hide');
    };

    this.setDemofileFromFolder = function(filename){
        jQuery("input[name='product_demo_file_name_" + this.cElName + "']").val(filename);
        jQuery('#demofilesModal').modal('hide');
    };

    this.setSalefileFromFolder = function(filename){
        jQuery("input[name='product_file_name_" + this.cElName + "']").val(filename);
        jQuery('#salefilesModal').modal('hide');
    };

    this.product_images_prevAjaxQuery = null;
    this.product_images_request = function(position, url, filter){
        var data = {};
        data['position'] = position;
        data['filter'] = filter;
        if (that.product_images_prevAjaxQuery){
            that.product_images_prevAjaxQuery.abort();
        }
        that.product_images_prevAjaxQuery = jQuery.ajax({
            url: url,
            dataType: 'html',
            data : data,
            beforeSend: function() {
                jQuery('#product_images').empty();
                jQuery('.sbox-content-string').append('<div id="product_images-overlay"></div>');
            },
            success: function(html){
                jQuery('#product_images-overlay').remove();
                jQuery('#product_images').html(html).fadeIn();
            }
        });
    };
    
    this.changeProductField = function(obj){
        isChecked = jQuery(obj).is(':checked');
        var div_inputs = jQuery(obj).parents('div:first');        
        if (isChecked) {
            div_inputs.find(".product_img_name").hide();
            div_inputs.find(".product_file_image").hide();
            div_inputs.find(".product_file_image input").val('');
            div_inputs.find('.product_folder_image').show();
        } else {
            div_inputs.find(".product_img_name").show();
            div_inputs.find(".product_folder_image").hide();
            div_inputs.find(".product_folder_image input[type=text]").val('');
            div_inputs.find('.product_file_image').show();
        }
    };

    this.getListOrderItems = function(){
        var max_count = this.end_number_order_item + 1;
        var product = {};
        for(var a=1; a<=max_count; a++){
            var detal_product = {};
            product_id = jQuery('input[name="product_id['+ a +']"]').val();
            if (!product_id) continue;
            detal_product['product_id'] = product_id;
            detal_product['product_tax'] = jQuery('input[name="product_tax['+a+']"]').val();
            detal_product['product_name'] = jQuery('input[name="product_name['+a+']"]').val();
            detal_product['product_ean'] = jQuery('input[name="product_ean['+a+']"]').val();
            detal_product['product_attributes'] = jQuery('input[name="product_attributes['+a+']"]').val();
            detal_product['product_freeattributes'] = jQuery('input[name="product_freeattributes['+a+']"]').val();
            detal_product['thumb_image'] = jQuery('input[name="thumb_image['+a+']"]').val();
            detal_product['weight'] = jQuery('input[name="weight['+a+']"]').val();
            detal_product['delivery_times_id'] = jQuery('input[name="delivery_times_id['+a+']"]').val();
            detal_product['vendor_id'] = jQuery('input[name="vendor_id['+a+']"]').val();
            detal_product['product_quantity'] = jQuery('input[name="product_quantity['+a+']"]').val();
            detal_product['product_item_price'] = jQuery('input[name="product_item_price['+a+']"]').val();
            detal_product['order_item_id'] = jQuery('input[name="order_item_id['+a+']"]').val();
            product[a] = detal_product;
        }
        return product;
    };

    this.getOrderData = function(){
        var data_order = {};
        jQuery(".jshop_address input, .jshop_address select").each(function(){
            var name = jQuery(this).attr('name');
            data_order[name] = jQuery(this).val();
        });

        data_order['user_id'] = jQuery('#user_id').val();
        data_order['currency_id'] = jQuery('select[name="currency_id"]').val();
        data_order['display_price'] = jQuery('select[name="display_price"]').val();
        data_order['lang'] = jQuery('select[name="lang"]').val();
        data_order['shipping_method_id'] = jQuery('select[name="shipping_method_id"]').val();
        data_order['payment_method_id'] = jQuery('select[name="payment_method_id"]').val();
        data_order['order_delivery_times_id'] = jQuery('select[name="order_delivery_times_id"]').val();
        data_order['order_payment'] = jQuery('input[name="order_payment"]').val();
        data_order['order_shipping'] = jQuery('input[name="order_shipping"]').val();
        data_order['order_package'] = jQuery('input[name="order_package"]').val();
        data_order['order_discount'] = jQuery('input[name="order_discount"]').val();
        data_order['coupon_code'] = jQuery('input[name="coupon_code"]').val();
        return data_order;
    };

    this.order_tax_calculate = function(){
        var user_id = jQuery('#user_id').val();
        var product = this.getListOrderItems();
        var data_order = this.getOrderData();
        data_order['product'] = product;

        var url = 'index.php?option=com_jshopping&controller=orders&task=loadtaxorder';
        if (user_id>0){
            url+='&admin_load_user_id='+user_id;
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {'data_order': data_order},
            dataType : "json"
        }).done(function(json) {
            jQuery('input[name="tax_percent[]"]').each(function() {
                if (!jQuery(this).closest('tr').hasClass('tax-manual')) {
                    jQuery(this).closest('tr').remove();
                }
            });
            for (var i=0;i<json.length;i++){
                var html="<tr class='bold'>";
                html+='<td class="right"><input type="text" class="small3 form-control" name="tax_percent[]" value="'+json[i]['tax']+'"/> %</td>';
                html+='<td class="left"><input type="text" class="small3 form-control" name="tax_value[]" onkeyup="jshopAdmin.updateOrderTotalValue();" value="'+json[i]['value']+'"/></td>';
                html+='</tr>';
                jQuery("#row_button_add_tax").before(html);
            }
            that.updateOrderTotalValue();
        });
    };

    this.order_shipping_calculate = function(){
        var user_id = jQuery('#user_id').val();
        var product = this.getListOrderItems();
        var data_order = this.getOrderData();
        data_order['product'] = product;

        var url = 'index.php?option=com_jshopping&controller=orders&task=loadshippingprice';
        if (user_id>0){
            url+='&admin_load_user_id='+user_id;
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {'data_order': data_order},
            dataType : "json"
        }).done(function(json){
            if (json){
                jQuery('input[name="order_shipping"]').val(json.shipping);
                jQuery('input[name="order_package"]').val(json.package);
            }else{
                jQuery('input[name="order_shipping"]').val('');
                jQuery('input[name="order_package"]').val('');
            }
            that.order_tax_calculate();
        });
    };

    this.order_payment_calculate = function(){
        var user_id = jQuery('#user_id').val();
        var product = this.getListOrderItems();
        var data_order = this.getOrderData();
        data_order['product'] = product;

        var url = 'index.php?option=com_jshopping&controller=orders&task=loadpaymentprice';
        if (user_id>0){
            url+='&admin_load_user_id='+user_id;
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {'data_order': data_order},
            dataType : "json"
        }).done(function(json){
            jQuery('input[name="order_payment"]').val(json.price);
            that.order_tax_calculate();
        });
    };

    this.order_discount_calculate = function(){
        var user_id = jQuery('#user_id').val();
        var product = this.getListOrderItems();
        var data_order = this.getOrderData();
        data_order['product'] = product;

        var url = 'index.php?option=com_jshopping&controller=orders&task=loaddiscountprice';
        if (user_id>0){
            url+='&admin_load_user_id='+user_id;
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {'data_order': data_order},
            dataType : "json"
        }).done(function(json){
            jQuery('input[name="order_discount"]').val(json.price);
            that.order_tax_calculate();
        });
    };

	this.setMainMenuActive = function(currentUrl) {
		var wrapper2 = document.getElementById('wrapper');
		var allLinks2 = wrapper2.querySelectorAll('a.no-dropdown, a.collapse-arrow, .menu-dashboard > a');
		allLinks2.forEach(link => {	
			if (currentUrl.indexOf(link.href) === 0) {
				link.setAttribute('aria-current', 'page');
				link.classList.add('mm-active');
				if (!link.parentNode.classList.contains('parent')) {
					const firstLevel = link.closest('.collapse-level-1');
					const secondLevel = link.closest('.collapse-level-2');
					if (firstLevel) firstLevel.parentNode.classList.add('mm-active');
					if (firstLevel) firstLevel.classList.add('mm-show');
					if (secondLevel) secondLevel.parentNode.classList.add('mm-active');
					if (secondLevel) secondLevel.classList.add('mm-show');
				}
			}
		});
	}

    this.reloadSelectMainCategory = function(obj) {
        if (jQuery('option:selected', obj).length > 1) {
            var main_cat_sel = '<select name="main_category_id" class="inputbox form-select" onchange="jshopAdmin.updateMainCategoryVal(this.value)">';
            jQuery('option:selected', obj).each(function(){
                main_cat_sel += '<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
            });
            main_cat_sel += '</select>';
            jQuery('td.main_category_select').html(main_cat_sel);
            var cur_val = jQuery('td.main_category_select').attr('val');
            if (cur_val && cur_val != '0') {
                jQuery('td.main_category_select select').val(cur_val);
            }
            jQuery('td.main_category_select').closest('tr').show();
        } else {
            jQuery('td.main_category_select').html('');
            jQuery('td.main_category_select').closest('tr').hide();
        }
    }

    this.updateMainCategoryVal = function(val) {
        jQuery('td.main_category_select').attr('val', val);
    }

    this.productExtrafieldOptionPopup = function(obj, action) {
        let tr = jQuery(obj).closest('tr');
        let ef_id = tr.attr('extrafieldid');        
        let product_id = jQuery('input[name=product_id]').val();
        let ef_val_id = jQuery('.prod_extrafield_values input[type=hidden]', tr).val() ?? 0;
        let btn = jQuery(obj).closest('tr').find('.prod_extrafield_btn button');

        jQuery('#extrafields_option_popup .modal-title').html(btn.attr('title'));
        jQuery('#extrafields_option_popup input[name=new_ef_option_ef_id]').val(ef_id);
        jQuery('#extrafields_option_popup input[name=new_ef_option_ef_val_id]').val(ef_val_id);
        jQuery('#extrafields_option_popup .new_option').val('');
        jQuery('#extrafields_option_popup').modal('show');
        if (action == 'edit' && ef_val_id > 0) {
            jshopAdmin.ajaxLoadAnimate().show();
            jQuery.ajax({
                type: "POST",
                url: 'index.php?option=com_jshopping&controller=products&task=get_product_extrafield_value',
                data: {'product_id': product_id, 'ef_id': ef_id, 'ef_val_id': ef_val_id},
                dataType : "json"
            }).done(function(json){
                if (json.name) {
                    jQuery('#extrafields_option_popup input[name^=new_ef_option_ef_name]').each(function(){        
                        let lang = jQuery(this).attr('language');
                        jQuery(this).val(json.name[lang]);
                    });
                }
                jQuery('#extrafields_option_popup input[name=new_ef_option_ef_val_id]').val(json.id);
                jshopAdmin.ajaxLoadAnimate().hide();
            });
        }
    }

    this.productExtrafieldOptionPopupSave = function() {
        let ef_id = jQuery('#extrafields_option_popup input[name=new_ef_option_ef_id]').val();
        let ef_val_id = jQuery('#extrafields_option_popup input[name=new_ef_option_ef_val_id]').val();    
        let options = {};    
        jQuery('#extrafields_option_popup input[name^=new_ef_option_ef_name]').each(function(){        
            options[jQuery(this).attr('language')] = this.value;
        });
        jshopAdmin.ajaxLoadAnimate().show();
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_jshopping&controller=productfieldvalues&task=edit_ajax",
            data: {'options': options, 'ef_id': ef_id, 'ef_val_id': ef_val_id, 'ajax': 1},
            dataType : "json"
        }).done(function(json){
            let tr = jQuery("tr[extrafieldid="+ef_id+"]");
            if (jQuery('.prod_extrafield_values select', tr).length) {
                jQuery('.prod_extrafield_values select', tr).append('<option value="'+json.id+'">'+json.text+'</option>');
                jQuery('.prod_extrafield_values select option[value='+json.id+']', tr).prop("selected", "selected");
            } else {
                jQuery('.prod_extrafield_values input[type=hidden]', tr).val(json.id);
                jQuery('.prod_extrafield_values .prod_extra_fields_uniq_val', tr).html(json.text);
            }
            jshopAdmin.ajaxLoadAnimate().hide();
        }).fail(function(){
            jshopAdmin.ajaxLoadAnimate().hide();
            alert('Loading data error ... ');
        });
        jQuery('#extrafields_option_popup').modal('hide');
    }

    this.productExtrafieldOptionPopupClear = function(obj){
        let tr = jQuery(obj).closest('tr');
        jQuery('.prod_extrafield_values input[type=hidden]', tr).val('0');
        jQuery('.prod_extrafield_values .prod_extra_fields_uniq_val', tr).html('');
    }

    this.productExtrafieldSearch = function(){
        let search = jQuery('.prod_extrafields_search').val().toLowerCase();
        if (search == '') {
            jQuery('table.list_prod_extrafields tr').show();
        } else {
            jQuery('table.list_prod_extrafields tr').each(function(){
                jQuery(this).hide();
                if (jQuery(".prod_extrafield_title", this).length) {
                    let text = jQuery(".prod_extrafield_title", this).html().toLowerCase();
                    if (text.indexOf(search) != -1) {
                        jQuery(this).show();
                    }
                }
            });
        }
        if (jQuery('.prod_extrafields_search_hide_unfilled').prop('checked')){
            jQuery('table.list_prod_extrafields tr').each(function(){
                if (jQuery(".prod_extrafield_values", this).length == 0) {
                    jQuery(this).hide();
                }
                if (jQuery(".prod_extrafield_values input[type=text]", this).length) {
                    if (jQuery(".prod_extrafield_values input[type=text]", this).val() == '') {
                        jQuery(this).hide();
                    }
                }
                if (jQuery(".prod_extrafield_values input[type=hidden]", this).length) {
                    let val = jQuery(".prod_extrafield_values input[type=hidden]", this).val();
                    if (val == '0' || val == '') {
                        jQuery(this).hide();
                    }
                }
                if (jQuery(".prod_extrafield_values select", this).length) {   
                    let val = jQuery(".prod_extrafield_values select", this).val();
                    if (val=='0' || val.length==0) {
                        jQuery(this).hide();
                    }
                }
            });
        }
    }

    this.getAddonKey = function(alias) {
        jshopAdmin.ajaxLoadAnimate().show();
        var url = 'index.php?option=com_jshopping&controller=addonscatalog&task=getaddonkey&alias='+alias+"&ajax=1";
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(url, (data) => {
            jQuery('input[name="key"]').val(data);
            jshopAdmin.ajaxLoadAnimate().hide();
        });
    }

    this.ajaxLoadAnimate = function(){
        let ajaxLoadAnimate =  jQuery('#ajaxLoadAnimate');
        if (!ajaxLoadAnimate.length){
            ajaxLoadAnimate = jQuery('<div id="ajaxLoadAnimate"></div>');
            jQuery('body').append(ajaxLoadAnimate);
        }
        return ajaxLoadAnimate;
    }

    this.windowPopup = function(obj) {
        let url = jQuery(obj).attr('patch');
        let name = jQuery(obj).attr('winname') ?? 'win';
        window.open(url, name, 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=600,directories=no,location=no');
    }

    this.toggleOrderEditShippingSelect = function() {
        let sid = jQuery('.order_edit #shipping_method_id').val();
        jQuery('.order_edit #shipping_forms_container .shipping_forms').hide();
        jQuery('.order_edit #shipping_forms_container #shipping_form_' + sid).show();
        if (jQuery('.order_edit #shipping_forms_container #shipping_form_' + sid).length) {
            jQuery('.order_edit textarea[name=shipping_params]').hide();
        } else {
            jQuery('.order_edit textarea[name=shipping_params]').show();
        }
    }
}

var jshopAdmin = new jshopAdminClass();

jQuery(document).ready(function(){
    jQuery('.js-stools-btn-clear').click(function(){
        let closest = jQuery(this).closest('.js-filters');
        let selects = jQuery('select', closest);
        selects.each(function(){
            jQuery(this).val(0);
            let def_value = jQuery(this).attr('default-value');
            if (typeof(def_value) != 'undefined') {
                jQuery(this).val(def_value);  
            }
        });
        jQuery('input', closest).val('');
        this.form.submit();
    });

    jQuery(document).on('click', 'a.js_window_popup', function(){        
        jshopAdmin.windowPopup(this);
        return false;
    });

    jQuery(".joomla-tabs [data-toggle='tab']").on("click", function(e){
        e.preventDefault();
        var allLink = jQuery(this).closest(".joomla-tabs");
        allLink.find(".nav-link").removeClass("active");
        jQuery(this).addClass("active");

        allLink.siblings(".tab-content").find(".tab-pane").removeClass("active");
        allLink.siblings(".tab-content").find(jQuery(this).attr("href")).addClass("active");
    });
	
	jQuery(document).on('change', '.shop-list-order select[name^=select_status_id]', function(){
		jQuery(this).closest('td').find('.update_status_panel').removeClass('d-none');
	});

    jQuery(document).on('click', '.jshop_edit .extrafields_btn_add', function(){
        jshopAdmin.productExtrafieldOptionPopup(this, 'add');
        return false;
    });
    jQuery(document).on('click', '.jshop_edit .extrafields_btn_edit,.jshop_edit .prod_extrafield_values .prod_extra_fields_uniq_val', function(){
        jshopAdmin.productExtrafieldOptionPopup(this, 'edit');
        return false;
    });
    jQuery(document).on('click', '.jshop_edit .extrafields_btn_clear', function(){
        jshopAdmin.productExtrafieldOptionPopupClear(this);
        return false;
    });

    jQuery(document).on('click', '#extrafields_option_popup .btn-save', function(){
        jshopAdmin.productExtrafieldOptionPopupSave();
    });

    jQuery('.prod_extrafields_search, .prod_extrafields_search_hide_unfilled').on('input', function(){
        jshopAdmin.productExtrafieldSearch();
    });

    jQuery('a.a_addon_update').on('click', function(){
        let btn = jQuery(this);
        if (btn.attr('j_ver')) {
            jQuery('#update_popup .j-version span').html(btn.attr('j_ver'));
            jQuery('#update_popup .j-version').show();
        } else {
            jQuery('#update_popup .j-version').hide();
        }
        if (btn.attr('js_ver')) {
            jQuery('#update_popup .js-version span').html(btn.attr('js_ver'));
            jQuery('#update_popup .js-version').show();
        } else {
            jQuery('#update_popup .js-version').hide();
        }
        jQuery('#update_popup a.btn_update').attr('href', btn.attr('xhref'));
        jQuery('#update_popup').modal('show');
    });

    jshopAdmin.toggleOrderEditShippingSelect()
    jQuery('.order_edit #shipping_method_id').on('change', function(){
        jshopAdmin.toggleOrderEditShippingSelect(); 
    });    

});
