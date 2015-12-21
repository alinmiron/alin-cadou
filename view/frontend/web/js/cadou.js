/*define(
 [
 'jquery',
 'uiComponent'
 ],
 function ($, Component) {
 'use strict';
 console.info(Component);
 $(document).ready(function(){
 //        alert('Test JS 1');
 });
 return Component.extend({
 _create: function() {
 console.log('created');

 },

 });
 });
 */

define([
    'jquery',
    'jquery/ui',
    'Magento_Catalog/js/catalog-add-to-cart',
    'Magento_Ui/js/modal/modal'
], function($){

    $.widget('mage.catalogAddToCart', $.mage.catalogAddToCart, {
        product:undefined,
       // cart:undefined,
        ajaxSubmit: function(form) {
            this._super(form);
            var _this = this;
            $(document).ajaxSuccess(function(event, xhr, settings){
                if (settings.type == 'POST' &&  settings.url == form.attr('action')){
                    this.product = form.serialize();
                }
                if (this.product!=undefined){
                    if (settings.type == 'GET' && xhr.status == 200){
                        if (xhr.responseJSON !== undefined  && xhr.responseJSON.cart !== undefined){
                           // this.cart = xhr.responseJSON.cart;
                          //  console.log($(_this.options.minicartSelector));
                        }
                        showModalCadou(this);
                    }
                }
            });
        }

    });
    return $.mage.catalogAddToCart;
});

function showModalCadou(obj1){
    var _product = obj1.product;
  //  var _cart = obj1.cart;
    obj1.product = undefined;

    require(
        [
            'jquery',
            'Magento_Ui/js/modal/modal',
            'mage/calendar'
        ],
        function(
            $,
            modal
        ) {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Send this product as present to',
                buttons: [{
                    text: $.mage.__('Close'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                },
                    {
                        text: 'Ok',
                        class: '',
                        click: function() {
                            $('div#cadou-form-modal').find('div#message').remove();
                            var self = this;
                            $.ajax({
                                url: "/cadou/save/",
                                type:"POST",
                                data:{'form':$('#cadou-form-modal > form').serialize(),
                                     'product': _product
                                    //'cart':_cart
                                    },
                                dataType:"json",
                                success:function(data){
                                    console.log(data);
                                    if (data.result === 1){
                                        self.closeModal();
                                    }
                                    else
                                    {
                                        $('#cadou-form-modal').prepend('<div id="message" style="color:red;">'+data.error+'</div>');
                                    }
                                },
                                fail:function(data){
                                    console.info(data);
                                }
                            });

                        }
                    }
                ]
            };
             var popup = modal(options, $('#cadou-form-modal'));
            $('#cadou-form-modal').modal('openModal');
        }
    );
}
