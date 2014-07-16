define(function(require, exports, module) {
    var Widget = require('widget');

    var TagCollector = Widget.extend({
        events: {
            'click [data-role=item-add]': 'addItem',
            'keydown [data-role=item-input]': 'addItemByEnter',
            'click [data-role=item-delete]': 'deleteItem'
        },
        setup: function(){
            this._initItem();
        },
        _initItem: function(){
            var itemTemplate = this.$('[data-role=item-template]');
            var modal = this.$('[data-role=modal]');
            this.set('itemTemplate',itemTemplate);
            this.set('modal',modal);
        },
        addItemHtml: function(value){
            var itemTemplate = this.get('itemTemplate');
            var modal = this.get('modal');
            var html = itemTemplate.html();
            var itemCurrent = itemTemplate.find('[data-role=item]')
                                .text(value)
                                .parents('[data-role="item-template"]');
            modal.append(itemCurrent.html());
            this.clearInput();
            // console.log(itemCurrent.html());
        }, 
        clearInput: function(){
            this.$('[data-role=item-input]').val('');
        },
        deleteItem:function(e){
            $(e.currentTarget).parents('[data-role=item-box]').remove();
            console.log(e.currentTarget);
        },
        addItem: function() {
            var value = this.$('[data-role=item-input]').val();
            this.trigger('beforeEnterValue', value);
            if (value.length == '' || value == '') {
                return ;
            }
            this.addItemHtml(value);
        },
        addItemByEnter: function(e){
            if (e.which == 13) {
                // e.preventDefault();

                this.addItem();
            };
        },

    });

    module.exports = TagCollector;

});