jQuery(document).ready(function($){
    var selector = '#choiceOptions';
    var options = new Vue({
        el: selector,
        data: questionData,
        methods: {
            addOption: function (event) {
                var dataHolder = this.$data.newOption;
                if(dataHolder.text){
                    var option = {};
                    Object.assign(option,dataHolder);
                    this.$data.options.push(option);
                    dataHolder.text = '';
                    dataHolder.points = '';
                    dataHolder.right = false;
                    $($(event.currentTarget).closest('tr').find('input')[0]).focus();
                }else{
                    // show message?
                }
            }
        },
        computed: {
            printData: function () {
                return JSON.stringify(this.$data.options);
            }
        }
    });
    $('[assignEnterHit]').on('keypress',function(e){
        if(e.charCode==13){
            $($(this).attr('assignEnterHit')).click();
            e.preventDefault();
            e.stopPropagation();
        }
    })
});