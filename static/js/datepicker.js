    var DatePicker = function () {
        return {
            init:function(){
                 //init date pickers
                $('.date-picker').datepicker({
                    rtl: Metronic.isRTL(),
                    autoclose: true
                });

                $(".datetime-picker").datetimepicker({
                    isRTL: Metronic.isRTL(),
                    autoclose: true,
                    todayBtn: true,
                    pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
                    minuteStep: 10
                });
            }
        }
    }();
