jQuery(document).ready(function($) {

    if (typeof Backbone != 'undefined' && typeof Marionette != 'undefined') {

        var nfpaypalRadio = Backbone.Radio;
        var mycontroller = Marionette.Object.extend({

            initialize: function() {

               this.listenTo( nfpaypalRadio.channel( 'setting-paypal_standard_billing_cycle_type' ),     'attach:setting',      this.defaultFields );

                this.listenTo(nfpaypalRadio.channel('setting-name-paypal_standard_billing_cycle_number'), 'init:settingModel', this.registerBillingCycleNumberListener);
                this.listenTo(nfpaypalRadio.channel("actionSetting-paypal_standard_billing_cycle_type"), 'update:setting', this.triggerCycleNumberUpdate);
            },
            defaultFields: function( settingModel, dataModel) {
             this.triggerCycleNumberUpdate( dataModel, settingModel );


             },
            registerBillingCycleNumberListener: function(model) {
                model.listenTo(nfpaypalRadio.channel('paypal_standard_billing_cycle_number'), 'update:BillingCycle', this.updateBillingCycle, model);

            },
            triggerCycleNumberUpdate: function(dataModel, settingModel) {
                var prev = dataModel.get('paypal_standard_billing_cycle_number');
                var type = dataModel.get('paypal_standard_billing_cycle_type');
                var min = 1;
                var max = 0;
                switch (type) {
                    case "D":
                        max = 100;
                        break;
                    case "W":
                        max = 52;
                        break;
                    case "M":
                        max = 12;
                        break;
                    case "Y":
                        max = 5;
                        break;
                }
                var a = [];
                for (var i = min; i <= max; i++) {
                    a.push({
                        label: i,
                        value: i
                    });
                }

                //data ={options:a, choice}
                nfpaypalRadio.channel('paypal_standard_billing_cycle_number').trigger('update:BillingCycle', a, prev);
            },
            updateBillingCycle: function(options, selected) {
                var $el = jQuery('#paypal_standard_billing_cycle_number');
                    $el.empty(); // remove old options
                var sel_options = this.get( 'options' );

                this.set('options', options)

                jQuery.each(options, function(key, value) {
                    if( selected == value.label ){
                        $el.append(jQuery('<option selected="selected"></option>').attr('value', value.label).text(value.label));
                    }else{
                        $el.append(jQuery('<option></option>').attr('value', value.label).text(value.label));
                    }

                });
            }
        })

         new mycontroller();
    }

});