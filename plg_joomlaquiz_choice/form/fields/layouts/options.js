jQuery(document).ready(function($){
    var selector = '#choiceOptions';

    Vue.directive('sortable', {
        inserted: function (el, binding) {
            var sortable = new Sortable(el, binding.value || {});
        }
    });

    var options = new Vue({
        el: selector,
        data: questionData,
        methods: {
            addOption: function (event) {
                var dataHolder = this.$data.newOption;
                if(dataHolder.text){
                    var option = {};
                    Object.assign(option,dataHolder);
                    option.ordering = this.$data.options.length+1;
                    this.$data.options.push(option);
                    dataHolder.text = '';
                    dataHolder.points = '';
                    dataHolder.right = false;
                    $($(event.currentTarget).closest('tr').find('input')[0]).focus();
                }else{
                    // show message?
                }
            },
            deleteOption: function (i) {
                this.$data.deleteOptions.push(this.$data.options[i].id);
                this.$data.options.splice(i,1);
            },
            reorder: function (obj) {
                var movedItem = this.$data.options.splice(obj.oldIndex, 1)[0];
                this.$data.options.splice(obj.newIndex, 0, movedItem);
            }
        },
        computed: {
            printData: function () {
                return JSON.stringify(this.$data.options);
            },
            printDelete: function () {
                return JSON.stringify(this.$data.deleteOptions);
            }
        }
    });

    $('[assignEnterHit]').on('keypress',function(e){
        if(e.charCode==13){
            $($(this).attr('assignEnterHit')).click();
            e.preventDefault();
            e.stopPropagation();
        }
    });
});