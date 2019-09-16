
(function ($) {

    $.fn.customerkey = function (options) {

        var customerkey = $("body").data("customerkey");

        if (typeof options == "string") {
            if (customerkey && customerkey[options]) {
                customerkey[options]();
            }
            return;
        }

        new $.customerkey.core(this, $.extend({}, options));
    };

    $.customerkey = { version: '1.0.0' };

    $.customerkey.core = function (elm, options) {
        this.element = $(elm);
        this.options = $.extend({}, options);

        this._structure = {
            ids: {
                tabsContainer: 'tabsContainer',
                tabGenerate: 'tabGenerate',
                tabList: 'tabList',
                contentGenerate: 'contentGenerate',
                contentList: 'contentList',
                labelResult: 'labelResult',
                customerName: 'customerName',
                numberKeys: 'numberKeys',
                customerList: 'customerList'
            }
        };

        this.init();
    };

    $.customerkey.core.prototype = {

        /**
         * generates all html 
         * @name init()
        */
        init: function () {

            this.element.html('<div class="tabsContainer">' +
                '<ul class="tabs">' +
                '<li id="' + this._structure.ids.tabGenerate + '" class="active">Generate keys</li>' +
                '<li id="' + this._structure.ids.tabList + '">List customers</li>' +
                '</ul>' +
                '</div>' +
                '<div class="contentContainer">' +
                '<div id="' + this._structure.ids.contentGenerate + '">' +
                '<div id="' + this._structure.ids.labelResult + '"></div>' +
                '<table>' +
                '<tr><td><label>Customer Name: </label></td><td><input type="text" id="' + this._structure.ids.customerName + '" /></td></tr>' +
                '<tr><td><label>Number Keys: </label></td><td><input type="text" id="' + this._structure.ids.numberKeys + '" /></td></tr>' +
                '<tr><td><button id="' + this._structure.ids.generateKey + '">Generate</button></td></tr>' +
                '</table>' +
                '</div>' +
                '<div id="' + this._structure.ids.contentList + '">' +
                '<ul id="' + this._structure.ids.customerList + '">' +
                '</ul>' +
                '</div>' +
                '</div>');

            this.bind();
        },

        /**
         * bind all events
         * @name bind()
        */
        bind: function () {

            var $this = this;

            $('#' + this._structure.ids.tabGenerate)
                .click(function (e) {
                    e.preventDefault();

                    $(this).addClass('active');

                    $('#' + $this._structure.ids.tabList)
                        .removeClass('active');

                    $('#' + $this._structure.ids.contentGenerate)
                        .css('display', 'block');
                    $('#' + $this._structure.ids.contentList)
                        .css('display', 'none');

                    $('#' + $this._structure.ids.labelResult)
                        .html("")
                        .removeAttr('class');

                    $('#' + $this._structure.ids.customerName)
                        .val("");
                    $('#' + $this._structure.ids.numberKeys)
                        .val("");

                });

            $('#' + this._structure.ids.tabList)
                .click(function (e) {
                    e.preventDefault();

                    $(this).addClass('active');

                    $('#' + $this._structure.ids.tabGenerate)
                        .removeClass('active');

                    $('#' + $this._structure.ids.contentGenerate)
                        .css('display', 'none');
                    $('#' + $this._structure.ids.contentList)
                        .css('display', 'block');

                    $this.showCustomerList();
                });

            $('#' + this._structure.ids.generateKey)
                .click(function (e) {
                    e.preventDefault();

                    if ($('#' + $this._structure.ids.customerName).val().trim().length == 0) {
                        alert("Please enter a customer name.");
                        return false;
                    }

                    if ($('#' + $this._structure.ids.numberKeys).val().trim().length == 0) {
                        alert("Please enter a number key(s).");
                        return false;
                    } else if (!$this.isValidNumber($('#' + $this._structure.ids.numberKeys).val())) {
                        alert("Number should be digits only.");
                        return false;
                    }

                    $('#' + $this._structure.ids.generateKey).attr('disabled', 'disabled');

                    var customer = {
                        Name: $('#' + $this._structure.ids.customerName).val()
                    };

                    $.ajax({
                        url: '/api/customer',
                        type: 'POST',
                        data: JSON.stringify({ Customer: customer, Numbers: $('#' + $this._structure.ids.numberKeys).val() }),
                        contentType: "application/json;charset=utf-8",
                        success: function (data) {
                            $('#' + $this._structure.ids.labelResult)
                                .html("Key(s) were generated successful.");
                            $('#' + $this._structure.ids.labelResult)
                                .addClass('success')
                                .css('display', 'block');

                            $('#' + $this._structure.ids.customerName)
                                .val("");
                            $('#' + $this._structure.ids.numberKeys)
                                .val("");

                            $('#' + $this._structure.ids.generateKey)
                                .removeAttr('disabled');
                        },
                        error: function (x, y, z) {
                            $('#' + $this._structure.ids.labelResult)
                                .html("Error while generate a key(s).");
                            $('#' + $this._structure.ids.labelResult)
                                .addClass('error')
                                .css('display', 'none');

                            $('#' + $this._structure.ids.generateKey)
                                .removeAttr('disabled');
                        }
                    });

                });

        },

        /**
         * generate and display list of customers
         * @name showCustomerList()
        */
        showCustomerList: function () {

            var $this = this;

            $('#' + $this._structure.ids.customerList).empty();

            $.ajax({
                url: '/api/customer',
                type: 'GET',
                dataType: 'json',
                success: function (customers) {

                    if (customers.length == 0) {
                        $('#' + $this._structure.ids.customerList).html('<div style="padding: 5px;">List is empty. Please first create a customer.</div>');
                        return;
                    }

                    $.each(customers, function (index, customer) {

                        var state = $.customerkey.storage.get('customerid_' + customer.Id)
                        var iconClass = 'icon-close';

                        if (!!state) {
                            iconClass = 'icon-open';
                        }

                        var node = $('<li customer-id="' + customer.Id + '">' +
                            '<span class="customerlist-icon ' + iconClass + '">+</span>' +
                            '<a class="customerlist-anchor" href="#">' + customer.Name + '</a>' +
                            '</li>').click(function (e) {

                                var icon = $(this).find('.customerlist-icon');

                                if (icon.hasClass('icon-close')) {
                                    icon.html('-');
                                    icon.removeClass('icon-close');
                                    icon.addClass('icon-open');

                                    $this.loadKeys(this, customer.Id);

                                    $.customerkey.storage.set('customerid_' + customer.Id, '1');
                                }
                                else {
                                    icon.html('+');
                                    icon.removeClass('icon-open');
                                    icon.addClass('icon-close');

                                    $(this).find('.customerlist-children').remove();

                                    $.customerkey.storage.del('customerid_' + customer.Id);
                                }

                                return false;
                            });

                        $('#' + $this._structure.ids.customerList).append(node);

                        if (!!state) {
                            var icon = $('#' + $this._structure.ids.customerList + " > li[customer-id='" + customer.Id + "']").find('.customerlist-icon')

                            icon.html('-');
                            icon.removeClass('icon-close');
                            icon.addClass('icon-open');

                            $this.loadKeys(node, customer.Id);
                        }

                    });
                },
                error: function (x, y, z) {
                    console.log('error');
                }
            });

        },

        /**
         * load keys by customer Id
         * @name loadKeys()
         * @param  {DOMElement} node DOM element where load html with keys
         * @param  {Integer} customerId customer Id
        */
        loadKeys: function (node, customerId) {

            var $this = this;

            $.ajax({
                url: '/api/key/' + customerId,
                type: 'GET',
                dataType: 'json',
                success: function (keys) {

                    if (keys.length > 0) {

                        var strResult = '<ul class="customerlist-children">';

                        $.each(keys, function (index, key) {
                            strResult += '<li  class="customerlist-node">' + key.Value + '</li>';
                        });

                        strResult += '</ul>';

                        $(node).find('.customerlist-children').remove();
                        $(node).append(strResult);
                    }

                },
                error: function (x, y, z) {
                    console.log('error');
                }
            });

        },

        /**
         * checks input value for digits
         * @name isValidNumber()
         * @param  {Integer} number input value
         */
        isValidNumber: function (number) {
            var matchArray = number.match(/^[1-9]+\d*$/);

            if (matchArray == null) {
                return false;
            }

            return true;
        }
    };

    /**
     * works with local storage
    */
    $.customerkey.storage = {
        set: function (key, val) { return window.localStorage.setItem(key, val); },
        get: function (key) { return window.localStorage.getItem(key); },
        del: function (key) { return window.localStorage.removeItem(key); }
    };

})(jQuery);