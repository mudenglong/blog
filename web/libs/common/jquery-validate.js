define(function(require, exports, module) {
    require('jquery.validate')($);

    var rules = [
        [
            'chinese',
            function (val) {
                return /^([\u4E00-\uFA29]|[\uE7C7-\uE7F3])*$/i.test(val);
            },
            '必须是中文字'
        ],
        [
            'chinese_alphanumeric',
            function (val) {
                return /^([\u4E00-\uFA29]|[a-zA-Z0-9_])*$/i.test(val);
            },
            '必须是中文字、英文字母、数字及下划线组成'
        ],
        [
            'alphanumeric',
            function (val) {
                return /^[a-zA-Z0-9_]+$/i.test(val);
            },
            '必须是英文字母、数字及下划线组成'
        ],
        [
            'currency',
            function (val) {
                return /^(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,2})?$/i.test(val);
            },
            '请输入合法的,如:200, 221.99, 0.99, 0等'
        ],        
        [
            'idcard',
            function (val) {
                return /^\d{17}[0-9xX]$/.test(val);
            },
            '格式不正确!'
        ],
        [
            'password',
            function (val) {
                return /^[\S]{4,20}$/i.test(val);
            },
            '只能由4-20个字符组成'
        ],
        [
            'qq',
            function (val) {
                return /^[1-9]\d{4,}$/.test(val);
            },
            '格式不正确'
        ],
        [
            'integer',
            function (val) {
                return /^[+-]?\d+$/.test(val);
            },
            '必须为整数'
        ],
        [
            'remotecheck',
            function(val, options, commit) {
          
                var returnMsg = true; 
                var url = $(options).data('url') || null;
                $.ajax({
                    type:"get",
                    url:url,
                    data: {value:val},
                    dataType:"json",
                    async:false,
                    success:function (response) {
                        commit[0](response.success, response.message);
                        returnMsg = response.success; 
                    }});

                return returnMsg;
            }
        ]
    ];

    exports.inject = function(jQuery) {
        console.log('inject');
       
        jQuery.validator.setDefaults({
            errorClass: "valid-error"
           
        });

        // <div class="help-block" style=""><span class="text-danger">请输入标签名称</span></div>

        $.each(rules, function(index, rule){
            console.log(index);
            jQuery.validator.addMethod.apply(jQuery, rule);

        });

        

    };

});