define(function(require, exports, module) {
    require('jquery.validate')($);

    require('jquery.select2-css');
    require('jquery.select2');

    exports.run = function() {
        

        $("#jswidgetForm").validate({
            rules: {
                'jswidget_title': {
                    required: true,
                    minlength: 2
                },
    
                'jswidget_url': "required",
            },
            messages: {
                'jswidget_title':{
                    required: "请输入标题",
                    minlength: "标题的最小长度为2"
                },
                'jswidget_url': "请输入URL",
            }
        });


        var tagUrl = '/tag/match_jsonp'
        $('#jswidget_tags').select2({

            ajax: {
                url: tagUrl + '#',
                dataType: 'json',
                quietMillis: 100,
                data: function(term, page) {
                    return {
                        q: term,
                        page_limit: 10
                    };
                },
                results: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {

                        results.push({
                            id: item.name,
                            name: item.name
                        });
                    });

                    return {
                        results: results
                    };

                }
            },
            initSelection: function(element, callback) {
                var data = [];
                $(element.val().split(",")).each(function (s0, name) {
                    data.push({
                        id: name,
                        name: name
                    });
                });
                callback(data);
            },
            formatSelection: function(item) {
                return item.name;
            },
            formatResult: function(item) {
                return item.name;
            },
            width: 'off',
            multiple: true,
            placeholder: "请输入标签",
            width: 'off',
            multiple: true,
            createSearchChoice: function() {
                return null;
            },
            maximumSelectionSize: 17
        });




        // tag 转为 后台可识别的字符
        // $('#jswidgetForm').submit(function(e) {
        //     debugger;
        //     e.preventDefault();
        //     return;        
        // });

    };

});

