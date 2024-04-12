var jshopClass = function(){

    var that = this;

    this.highlightField = function(field){
        jQuery('#'+field).addClass('fielderror');
    };

    this.unhighlightField = function(formName){
        var form = document.forms[formName];
        var countElements = form.length;
        for (i = 0; i < countElements; i++){
            if (form.elements[i].type == 'button' || form.elements[i].type == 'submit' || form.elements[i].type == 'radio' || form.elements[i].type == 'hidden') continue;
            jQuery(form.elements[i]).removeClass('fielderror');
        }
    };

    this.isEmpty = function(value){
        var pattern = /\S/;
        return ret = (pattern.test(value)) ? (0) : (1);
    };

    this.checkMail = function(value){
       var pattern = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
       return ret = (pattern.test(value)) ? (1) : (0);
    };

    this.Equal = function(value1, value2){
      return (value1 == value2);
    };

    this.checkAGBAndNoReturn = function(agb, no_return){
        var result1=result2=true;
        if (agb=='1') result1 = this.checkAGB();
        if (no_return=='1') result2 = this.checkNoReturn();
        if (result1 && result2)
            return true;
        else
            return false;
    };

    this.checkAGB = function(){
        if (jQuery("#agb").prop("checked")){
            jQuery(".row_agb").removeClass('fielderror');
            return true;
        }else{
            jQuery(".row_agb").addClass('fielderror');
            jQuery('#agb').focus();
            return false;
        }
    };

    this.checkNoReturn = function(){
        if (jQuery("#no_return").prop("checked")){
            jQuery(".row_no_return").removeClass('fielderror');
            return true;
        }else{
            jQuery(".row_no_return").addClass('fielderror');
            jQuery('#no_return').focus();
            return false;
        }
    };
	
	this.getActivePaymentMethod = function(){
		return jQuery("input[name='payment_method']:checked").val();
	}
    
    this.showPaymentForm = function(){
        var active = this.getActivePaymentMethod();
        jQuery("*[id^='tr_payment_']").hide();
        jQuery('#tr_payment_'+active).show();
    };

    this.checkPaymentForm = function(){
		var active = this.getActivePaymentMethod();
        if (active && typeof(jshopParams)!='undefined' && jshopParams['check_'+active]){
            return jshopParams['check_'+active]();
        }
		return true;
    };

    this.isInt_5_8 = function(value){
        var pattern = /^(\d){5,8}$/;
        return ret = (pattern.test(value)) ? (1) : (0);
    };

    this.validateShippingMethods = function(){
        var inputs = jQuery("#table_shippings input[name='sh_pr_method_id']");
        for (var i=0; i<inputs.length; i++){
            if (inputs[i].checked) return true;
        }
        return false;
    };

    this.showShippingForm = function(id){
        jQuery("div.shipping_form").removeClass('shipping_form_active');
        jQuery("#shipping_form_"+id).addClass('shipping_form_active');
    };

    this.submitListProductFilterSortDirection = function(){
        jQuery('#orderby').val(jQuery('#orderby').val() ^ 1);
        this.submitListProductFilters();
    };

    this.submitListProductFilters = function(){
        jQuery('#sort_count').submit();
    };

    this.clearProductListFilter = function(){
        jQuery("#manufacturers").val("0");
        jQuery("#categorys").val("0");
        jQuery("#price_from").val("");
        jQuery("#price_to").val("");
        this.submitListProductFilters();
    };
    
    this.showVideo = function(idElement, width, height){
        var videofile = jQuery("#"+idElement).attr("href");
		
        jQuery('.video_full').hide();
        jQuery('#hide_' + idElement).attr("href", videofile);
        jQuery('a.lightbox').hide();
        jQuery('#main_image').hide();
        jQuery(".product_label").hide();
        jQuery("#videoshophtml5").remove();
        
        var videoOptions = {
            id: 'videoshophtml5',
            src: videofile,
            controls: true
        };
		if (width!='0'){
			videoOptions.width = width;
		}
		if (height!='0'){
			videoOptions.height = height;
		}
		if (width=='0' && height=='0'){
			videoOptions.width = '100%';
		}
        if (jshopParams.joomshoppingVideoHtml5Type!=''){
            videoOptions.type = jshopParams.joomshoppingVideoHtml5Type;
        }
        var video = jQuery('<video />', videoOptions);
        video.appendTo(jQuery('.image_middle'));
        
    };

    this.showVideoCode = function(idElement){
        jQuery('.video_full:not(#hide_' + idElement + ')').hide();
        jQuery('a.lightbox').hide();
        jQuery('#main_image').hide();
        jQuery(".product_label").hide();
        jQuery("#videoshophtml5").remove();
        jQuery('#hide_' + idElement).show();
    };

    this.showImage = function(id){
        jQuery('.video_full').hide();
        jQuery("#videoshophtml5").remove();
        jQuery('a.lightbox').hide();
        jQuery("#main_image_full_"+id).show();
        jQuery(".product_label").show();
    };

    this.formatprice = function(price){
        if (typeof(jshopParams.decimal_count)==='undefined') jshopParams.decimal_count = 2;
        if (typeof(jshopParams.decimal_symbol)==='undefined') jshopParams.decimal_symbol = ".";
		if (typeof(jshopParams.format_currency)==='undefined') jshopParams.format_currency = "00 Symb";
        price = price.toFixed(jshopParams.decimal_count).toString();
        price = price.replace('.',jshopParams.decimal_symbol);
        res = jshopParams.format_currency.replace("Symb",jshopParams.currency_code);
        res = res.replace("00",price);
    return res;
    };

    this.prevAjaxHandler = null;
    this.reloadAttribEvents = [];
    this.extdataurlupdateattr = {};
    this.reloadAttribSelectAndPrice = function(id_select){
        var product_id = jQuery("#product_id").val();
        var qty = jQuery("#quantity").val();
        var data = {};
        data["change_attr"] = id_select;
        data["qty"] = qty;
        for(var i=0;i<jshopParams.attr_list.length;i++){
            var id = jshopParams.attr_list[i];
            data["attr["+id+"]"] = jshopParams.attr_value[id];
        }
        for(extdatakey in this.extdataurlupdateattr){
            data[extdatakey] = this.extdataurlupdateattr[extdatakey];
        }

        if (this.prevAjaxHandler){
            this.prevAjaxHandler.abort();
        }

        this.prevAjaxHandler = jQuery.getJSON(
            jshopParams.urlupdateprice,
            data,
            function(json){
                var reload_atribut = 0;
                for(var i=0;i<jshopParams.attr_list.length;i++){
                    var id = jshopParams.attr_list[i];
                    if (reload_atribut){
                        jQuery("#block_attr_sel_"+id).html(json['id_'+id]);
                    }
                    if (id == id_select) reload_atribut = 1;
                }

                jQuery("#block_price").html(json.price);
                jQuery("#pricefloat").val(json.pricefloat);

                if (json.basicprice){
                    jQuery("#block_basic_price").html(json.basicprice);
                }

                for(key in json){
                    if (key.substr(0,3)=="pq_"){
                        jQuery("#pricelist_from_"+key.substr(3)).html(json[key]);
                    }
                    if (key.substr(0,4)=="pqb_"){
                        jQuery("#pricelist_f_"+key.substr(4)+' .base .price').html(json[key]);
                    }
                }

                if (json.available=="0"){
                    jQuery("#not_available").html(jshopParams.translate_not_available);
                }else{
                    jQuery("#not_available").html("");
                }

                if (json.displaybuttons=="0"){
                    jQuery(".productfull .prod_buttons").hide();
                }else{
                    jQuery(".productfull .prod_buttons").show();
                }

                if (typeof json.ean !== 'undefined'){
                    jQuery("#product_code").html(json.ean);
                }
                if (typeof json.manufacturer_code !== 'undefined'){
                    jQuery("#manufacturer_code").html(json.manufacturer_code);
                }

                if (json.weight){
                    jQuery("#block_weight").html(json.weight);
                }
                if (json.pricedefault){
                    jQuery("#pricedefault").html(json.pricedefault);
                }
                if (typeof json.qty !== 'undefined'){
                    jQuery("#product_qty").html(json.qty);
                }
                if (json.oldprice){
                    jQuery("#old_price").html(json.oldprice);
                    jQuery(".old_price").show();
                }else{
                    jQuery(".old_price").hide();
                }

                if (json.block_image_thumb || json.block_image_middle){
					jQuery('.video_full').hide();
					jQuery("#videoshophtml5").remove();
                    jQuery("#list_product_image_thumb").html(json.block_image_thumb);
                    jQuery("#list_product_image_middle").html(json.block_image_middle);
					if (jshopParams.initJSlightBox) {
						jshop.initJSlightBox();
					}
                }

                if (typeof(json.demofiles)!='undefined'){
                    jQuery("#list_product_demofiles").html(json.demofiles);
                }

                if (json.showdeliverytime){
                    if (json.showdeliverytime=="0"){
                        jQuery(".productfull .deliverytime").hide();
                    }else{
                        jQuery(".productfull .deliverytime").show();
                    }
                }

                jQuery.each(that.reloadAttribEvents, function(key, handler){
                    handler.call(this, json);
                });

                that.reloadAttrValue();
            }
        );
    };

    this.setAttrValue = function(id, value){
        jshopParams.attr_value[id] = value;
        this.reloadAttribSelectAndPrice(id);
        this.reloadAttribImg(id, value);
    };

    this.reloadAttribImg = function(id, value){
        var path = "";
        var img = "";
        if (value=="0"){
            img = "";
        }else{
            if (jshopParams.attr_img[value]){
                img = jshopParams.attr_img[value];
            }else{
                img = "";
            }
        }

        if (img==""){
            path = jshopParams.liveimgpath;
            img = "blank.gif";
        }else{
            path = jshopParams.liveattrpath;
        }
        jQuery("#prod_attr_img_"+id).attr('src', path+"/"+img);
    };

    this.reloadAttrValue = function(){
        for(var id in jshopParams.attr_value){
            if (jQuery("input[name=jshop_attr_id\\["+id+"\\]]").attr("type")=="radio"){
                jshopParams.attr_value[id] = jQuery("input[name=jshop_attr_id\\["+id+"\\]]:checked").val();
            }else{
                jshopParams.attr_value[id] = jQuery("#jshop_attr_id"+id).val();
            }
        }
    };

    this.reloadPrices = function(){
        var qty = jQuery("#quantity").val();
        if (qty!=""){
            this.reloadAttribSelectAndPrice(0);
        }
    };

    this.showHideFieldFirm = function(type_id){
        if (type_id=="2"){
            jQuery("#tr_field_firma_code").show();
            jQuery('#tr_field_tax_number').show();
            jQuery('.required-company').addClass('required');
            jQuery('.required-company').attr('required', 'required');
        }else{
            jQuery("#tr_field_firma_code").hide();
            jQuery('#tr_field_tax_number').hide();
            jQuery('.required-company').removeClass('required');
            jQuery('.required-company').removeAttr('required');
        }
    };
    
    this.showHideAddressDelivery = function(val){
        if (val==1){
            jQuery('#div_delivery').show();            
            jQuery('#div_delivery .required-d').addClass('required');
            jQuery('#div_delivery .required-d').attr('required', 'required');
        }else{
            jQuery('#div_delivery').hide();
            jQuery('#div_delivery .required-d').removeClass('required');
            jQuery('#div_delivery .required-d').removeAttr('required');
        }
    }

    this.updateSearchCharacteristic = function(url, category_id){
        var data = {"category_id":category_id};
        jQuery.get(url, data, function(data){
            jQuery("#list_characteristics").html(data);
        });
    };
    
    this.initJSlightBox = function(){
        jQuery("a.lightbox").lightBox({
            imageLoading: jshopParams.liveurl+'components/com_jshopping/images/loading.gif',
            imageBtnClose: jshopParams.liveurl+'components/com_jshopping/images/close.gif',
            imageBtnPrev: jshopParams.liveurl+'components/com_jshopping/images/prev.gif',
            imageBtnNext: jshopParams.liveurl+'components/com_jshopping/images/next.gif',
            imageBlank: jshopParams.liveurl+'components/com_jshopping/images/blank.gif',
            txtImage: jshopParams.JSHOP_IMAGE,
            txtOf: jshopParams.JSHOP_OF
        });
    }

    this.registrationTestPassword = function(pass){
        jQuery.ajax({
            type: "POST",
            url: jshopParams.urlcheckpassword,
            data: {"pass": pass},
            dataType : "json"
        }).done(function(json){
            if (json.msg){
                jQuery('#reg_test_password').html(json.msg);
                jQuery('#reg_test_password').addClass('fielderrormsg');
                jQuery('#reg_test_password').removeClass('fieldpassok');
            }else{
                jQuery('#reg_test_password').html('');
                jQuery('#reg_test_password').removeClass('fielderrormsg');
                jQuery('#reg_test_password').addClass('fieldpassok');
            }
        });
    };
}
var jshop = new jshopClass();

jQuery(document).ready(function(){
	jQuery('.jshop #client_type').on('change', function(){
		jshop.showHideFieldFirm(this.value);
	});
	if (jQuery('.jshop #client_type').val()==2){
		jshop.showHideFieldFirm(2);
	}

    jQuery('.jshop .registrationTestPassword').on('keyup', function(){
        jshop.registrationTestPassword(this.value);
    });
	
	jQuery('.jshop input[name="delivery_adress"]').on('click',function(){        
        jshop.showHideAddressDelivery(this.value);
    });
	
	jQuery('#previewfinish_btn').on('click', function(){
		return jshop.checkAGBAndNoReturn(jQuery(this).attr('data-agb'), jQuery(this).attr('data-noreturn'));
	});
	
	jQuery('#clear_product_list_filter').on('click', function(){
		jshop.clearProductListFilter();
		return false;
	});
	jQuery('#submit_product_list_filter').on('click', function(){
		jshop.submitListProductFilters();
	});
	jQuery('.submit_product_list_filter').on('change', function(){
		jshop.submitListProductFilters();
	});
	jQuery('#submit_product_list_filter_sort_dir').on('click', function(){
		jshop.submitListProductFilterSortDirection();
	});
	
	jQuery('.jshop #filter_mob_head').on('click', function(){
		if (jQuery(this).hasClass('active')){
			jQuery(this).removeClass('active');
			jQuery('.jshop_list_product .filters').removeClass('active');
		}else{
			jQuery(this).addClass('active');
			jQuery('.jshop_list_product .filters').addClass('active');
		}
	});
	
    if (typeof(jshopParams)!=='undefined' && jshopParams.initJSlightBox){
        jshop.initJSlightBox();
    }

	jQuery(document).on('click', '.jshop .cart .cart_reload', function(){
       jQuery('.jshop form#updateCart').submit(); 
    });

	jQuery('.jshop #payment_form').on('submit', function(){
		return jshop.checkPaymentForm();
	});
    jQuery(".jshop #payment_form input[name='payment_method']").on('click', function(){
       jshop.showPaymentForm(); 
    });
    
    jQuery('.jshop #shipping_form').on('submit', function(){
        return jshop.validateShippingMethods();
    });
    jQuery(".jshop #table_shippings input[name='sh_pr_method_id']").on('click', function(){
       jshop.showShippingForm(jQuery(this).attr('data-shipping_id'));
    });
});