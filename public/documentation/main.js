/***
Wrapper/Helper Class for datagrid based on jQuery Datatable Plugin
***/
var Datatable = function() {

    var tableOptions; // main options
    var dataTable; // datatable object
    var table; // actual table jquery object
    var tableContainer; // actual table container object
    var tableWrapper; // actual table wrapper jquery object
    var tableInitialized = false;
    var ajaxParams = {}; // set filter mode
    var the;

    var countSelectedRecords = function() {
        var selected = $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', table).size();
        var text = tableOptions.dataTable.language.metronicGroupActions;
        if (selected > 0) {
            $('.table-group-actions > span', tableWrapper).text(text.replace("_TOTAL_", selected));
        } else {
            $('.table-group-actions > span', tableWrapper).text("");
        }
    };

    return {

        //main function to initiate the module
        init: function(options) {

            if (!$().dataTable) {
                return;
            }

            the = this;

            // default settings
            options = $.extend(true, {
                src: "", // actual table  
                filterApplyAction: "filter",
                filterCancelAction: "filter_cancel",
                resetGroupActionInputOnSuccess: true,
                loadingMessage: 'Loading...',
                dataTable: {
                    "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r><'table-responsive't><'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>", // datatable layout
                    "pageLength": 10, // default records per page
                    "language": { // language settings
                        // metronic spesific
                        "metronicGroupActions": "_TOTAL_ records selected:  ",
                        "metronicAjaxRequestGeneralError": "Could not complete request. Please check your internet connection",

                        // data tables spesific
                        "lengthMenu": "<span class='seperator'>|</span>View _MENU_ records",
                        "info": "<span class='seperator'>|</span>Found total _TOTAL_ records",
                        "infoEmpty": "No records found to show",
                        "emptyTable": "No data available in table",
                        "zeroRecords": "No matching records found",
                        "paginate": {
                            "previous": "Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First",
                            "page": "Page",
                            "pageOf": "of"
                        }
                    },

                    "orderCellsTop": true,
                    "columnDefs": [{ // define columns sorting options(by default all columns are sortable extept the first checkbox column)
                        'orderable': false,
                        'targets': [0]
                    }],

                    "pagingType": "bootstrap_extended", // pagination type(bootstrap, bootstrap_full_number or bootstrap_extended)
                    "autoWidth": false, // disable fixed width and enable fluid table
                    "processing": false, // enable/disable display message box on record load
                    "serverSide": true, // enable/disable server side ajax loading

                    "ajax": { // define ajax settings
                        "url": "", // ajax URL
                        "type": "POST", // request type
                        "timeout": 20000,
                        "data": function(data) { // add request parameters before submit
                            $.each(ajaxParams, function(key, value) {
                                data[key] = value;
                            });
                            App.blockUI({
                                message: tableOptions.loadingMessage,
                                target: tableContainer,
                                overlayColor: 'none',
                                cenrerY: true,
                                boxed: true
                            });
                        },
                        "dataSrc": function(res) { // Manipulate the data returned from the server
                            if (res.customActionMessage) {
                                App.alert({
                                    type: (res.customActionStatus == 'OK' ? 'success' : 'danger'),
                                    icon: (res.customActionStatus == 'OK' ? 'check' : 'warning'),
                                    message: res.customActionMessage,
                                    container: tableWrapper,
                                    place: 'prepend'
                                });
                            }

                            if (res.customActionStatus) {
                                if (tableOptions.resetGroupActionInputOnSuccess) {
                                    $('.table-group-action-input', tableWrapper).val("");
                                }
                            }

                            if ($('.group-checkable', table).size() === 1) {
                                $('.group-checkable', table).attr("checked", false);
                                $.uniform.update($('.group-checkable', table));
                            }

                            if (tableOptions.onSuccess) {
                                tableOptions.onSuccess.call(undefined, the, res);
                            }

                            App.unblockUI(tableContainer);

                            return res.data;
                        },
                        "error": function() { // handle general connection errors
                            if (tableOptions.onError) {
                                tableOptions.onError.call(undefined, the);
                            }

                            App.alert({
                                type: 'danger',
                                icon: 'warning',
                                message: tableOptions.dataTable.language.metronicAjaxRequestGeneralError,
                                container: tableWrapper,
                                place: 'prepend'
                            });

                            App.unblockUI(tableContainer);
                        }
                    },

                    "drawCallback": function(oSettings) { // run some code on table redraw
                        if (tableInitialized === false) { // check if table has been initialized
                            tableInitialized = true; // set table initialized
                            table.show(); // display table
                        }
                        App.initUniform($('input[type="checkbox"]', table)); // reinitialize uniform checkboxes on each table reload
                        countSelectedRecords(); // reset selected records indicator

                        // callback for ajax data load
                        if (tableOptions.onDataLoad) {
                            tableOptions.onDataLoad.call(undefined, the);
                        }
                    }
                }
            }, options);

            tableOptions = options;

            // create table's jquery object
            table = $(options.src);
            tableContainer = table.parents(".table-container");

            // apply the special class that used to restyle the default datatable
            var tmp = $.fn.dataTableExt.oStdClasses;

            $.fn.dataTableExt.oStdClasses.sWrapper = $.fn.dataTableExt.oStdClasses.sWrapper + " dataTables_extended_wrapper";
            $.fn.dataTableExt.oStdClasses.sFilterInput = "form-control input-xs input-sm input-inline";
            $.fn.dataTableExt.oStdClasses.sLengthSelect = "form-control input-xs input-sm input-inline";

            // initialize a datatable
            dataTable = table.DataTable(options.dataTable);

            // revert back to default
            $.fn.dataTableExt.oStdClasses.sWrapper = tmp.sWrapper;
            $.fn.dataTableExt.oStdClasses.sFilterInput = tmp.sFilterInput;
            $.fn.dataTableExt.oStdClasses.sLengthSelect = tmp.sLengthSelect;

            // get table wrapper
            tableWrapper = table.parents('.dataTables_wrapper');

            // build table group actions panel
            if ($('.table-actions-wrapper', tableContainer).size() === 1) {
                $('.table-group-actions', tableWrapper).html($('.table-actions-wrapper', tableContainer).html()); // place the panel inside the wrapper
                $('.table-actions-wrapper', tableContainer).remove(); // remove the template container
            }
            // handle group checkboxes check/uncheck
            $('.group-checkable', table).change(function() {
                var set = table.find('tbody > tr > td:nth-child(1) input[type="checkbox"]');
                var checked = $(this).prop("checked");
                $(set).each(function() {
                    $(this).prop("checked", checked);
                });
                $.uniform.update(set);
                countSelectedRecords();
            });

            // handle row's checkbox click
            table.on('change', 'tbody > tr > td:nth-child(1) input[type="checkbox"]', function() {
                countSelectedRecords();
            });

            // handle filter submit button click
            table.on('click', '.filter-submit', function(e) {
                e.preventDefault();
                the.submitFilter();
            });

            // handle filter cancel button click
            table.on('click', '.filter-cancel', function(e) {
                e.preventDefault();
                the.resetFilter();
            });
        },

        submitFilter: function() {
            the.setAjaxParam("action", tableOptions.filterApplyAction);

            // get all typeable inputs
            $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', table).each(function() {
                the.setAjaxParam($(this).attr("name"), $(this).val());
            });

            // get all checkboxes
            $('input.form-filter[type="checkbox"]:checked', table).each(function() {
                the.addAjaxParam($(this).attr("name"), $(this).val());
            });

            // get all radio buttons
            $('input.form-filter[type="radio"]:checked', table).each(function() {
                the.setAjaxParam($(this).attr("name"), $(this).val());
            });

            dataTable.ajax.reload();
        },

        resetFilter: function() {
            $('textarea.form-filter, select.form-filter, input.form-filter', table).each(function() {
                $(this).val("");
            });
            $('input.form-filter[type="checkbox"]', table).each(function() {
                $(this).attr("checked", false);
            });
            the.clearAjaxParams();
            the.addAjaxParam("action", tableOptions.filterCancelAction);
            dataTable.ajax.reload();
        },

        getSelectedRowsCount: function() {
            return $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', table).size();
        },

        getSelectedRows: function() {
            var rows = [];
            $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', table).each(function() {
                rows.push($(this).val());
            });

            return rows;
        },

        setAjaxParam: function(name, value) {
            ajaxParams[name] = value;
        },

        addAjaxParam: function(name, value) {
            if (!ajaxParams[name]) {
                ajaxParams[name] = [];
            }

            skip = false;
            for (var i = 0; i < (ajaxParams[name]).length; i++) { // check for duplicates
                if (ajaxParams[name][i] === value) {
                    skip = true;
                }
            }

            if (skip === false) {
                ajaxParams[name].push(value);
            }
        },

        clearAjaxParams: function(name, value) {
            ajaxParams = {};
        },

        getDataTable: function() {
            return dataTable;
        },

        getTableWrapper: function() {
            return tableWrapper;
        },

        gettableContainer: function() {
            return tableContainer;
        },

        getTable: function() {
            return table;
        }

    };

};
/**
Core script to handle the entire theme and core functions
**/
var App = function() {

    // IE mode
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var resizeHandlers = [];

    var assetsPath = '../assets/';

    var globalImgPath = 'global/img/';

    var globalPluginsPath = 'global/plugins/';

    var globalCssPath = 'global/css/';

    // theme layout color set

    var brandColors = {
        'blue': '#89C4F4',
        'red': '#F3565D',
        'green': '#1bbc9b',
        'purple': '#9b59b6',
        'grey': '#95a5a6',
        'yellow': '#F8CB00'
    };

    // initializes main settings
    var handleInit = function() {

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !!navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !!navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !!navigator.userAgent.match(/MSIE 10.0/);

        if (isIE10) {
            $('html').addClass('ie10'); // detect IE10 version
        }

        if (isIE10 || isIE9 || isIE8) {
            $('html').addClass('ie'); // detect IE10 version
        }
    };

    // runs callback functions set by App.addResponsiveHandler().
    var _runResizeHandlers = function() {
        // reinitialize other subscribed elements
        for (var i = 0; i < resizeHandlers.length; i++) {
            var each = resizeHandlers[i];
            each.call();
        }
    };

    // handle the layout reinitialization on window resize
    var handleOnResize = function() {
        var resize;
        if (isIE8) {
            var currheight;
            $(window).resize(function() {
                if (currheight == document.documentElement.clientHeight) {
                    return; //quite event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.                
                currheight = document.documentElement.clientHeight; // store last body client height
            });
        } else {
            $(window).resize(function() {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.
            });
        }
    };

    // Handles portlet tools & actions
    var handlePortletTools = function() {
        // handle portlet remove
        $('body').on('click', '.portlet > .portlet-title > .tools > a.remove', function(e) {
            e.preventDefault();
            var portlet = $(this).closest(".portlet");

            if ($('body').hasClass('page-portlet-fullscreen')) {
                $('body').removeClass('page-portlet-fullscreen');
            }

            portlet.find('.portlet-title .fullscreen').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
            portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');

            portlet.remove();
        });

        // handle portlet fullscreen
        $('body').on('click', '.portlet > .portlet-title .fullscreen', function(e) {
            e.preventDefault();
            var portlet = $(this).closest(".portlet");
            if (portlet.hasClass('portlet-fullscreen')) {
                $(this).removeClass('on');
                portlet.removeClass('portlet-fullscreen');
                $('body').removeClass('page-portlet-fullscreen');
                portlet.children('.portlet-body').css('height', 'auto');
            } else {
                var height = App.getViewPort().height -
                    portlet.children('.portlet-title').outerHeight() -
                    parseInt(portlet.children('.portlet-body').css('padding-top')) -
                    parseInt(portlet.children('.portlet-body').css('padding-bottom'));

                $(this).addClass('on');
                portlet.addClass('portlet-fullscreen');
                $('body').addClass('page-portlet-fullscreen');
                portlet.children('.portlet-body').css('height', height);
            }
        });

        $('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function(e) {
            e.preventDefault();
            var el = $(this).closest(".portlet").children(".portlet-body");
            var url = $(this).attr("data-url");
            var error = $(this).attr("data-error-display");
            if (url) {
                App.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: 'none'
                });
                $.ajax({
                    type: "GET",
                    cache: false,
                    url: url,
                    dataType: "html",
                    success: function(res) {
                        App.unblockUI(el);
                        el.html(res);
                        App.initAjax() // reinitialize elements & plugins for newly loaded content
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        App.unblockUI(el);
                        var msg = 'Error on reloading the content. Please check your connection and try again.';
                        if (error == "toastr" && toastr) {
                            toastr.error(msg);
                        } else if (error == "notific8" && $.notific8) {
                            $.notific8('zindex', 11500);
                            $.notific8(msg, {
                                theme: 'ruby',
                                life: 3000
                            });
                        } else {
                            alert(msg);
                        }
                    }
                });
            } else {
                // for demo purpose
                App.blockUI({
                    target: el,
                    animate: true,
                    overlayColor: 'none'
                });
                window.setTimeout(function() {
                    App.unblockUI(el);
                }, 1000);
            }
        });

        // load ajax data on page init
        $('.portlet .portlet-title a.reload[data-load="true"]').click();

        $('body').on('click', '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand', function(e) {
            e.preventDefault();
            var el = $(this).closest(".portlet").children(".portlet-body");
            if ($(this).hasClass("collapse")) {
                $(this).removeClass("collapse").addClass("expand");
                el.slideUp(200);
            } else {
                $(this).removeClass("expand").addClass("collapse");
                el.slideDown(200);
            }
        });
    };

    // Handles custom checkboxes & radios using jQuery Uniform plugin
    var handleUniform = function() {
        if (!$().uniform) {
            return;
        }
        var test = $("input[type=checkbox]:not(.toggle, .md-check, .md-radiobtn, .make-switch, .icheck), input[type=radio]:not(.toggle, .md-check, .md-radiobtn, .star, .make-switch, .icheck)");
        if (test.size() > 0) {
            test.each(function() {
                if ($(this).parents(".checker").size() === 0) {
                    $(this).show();
                    $(this).uniform();
                }
            });
        }
    };

    // Handlesmaterial design checkboxes
    var handleMaterialDesign = function() {

        // Material design ckeckbox and radio effects
        $('body').on('click', '.md-checkbox > label, .md-radio > label', function() {
            var the = $(this);
            // find the first span which is our circle/bubble
            var el = $(this).children('span:first-child');
              
            // add the bubble class (we do this so it doesnt show on page load)
            el.addClass('inc');
              
            // clone it
            var newone = el.clone(true);  
              
            // add the cloned version before our original
            el.before(newone);  
              
            // remove the original so that it is ready to run on next click
            $("." + el.attr("class") + ":last", the).remove();
        }); 

        if ($('body').hasClass('page-md')) { 
            // Material design click effect
            // credit where credit's due; http://thecodeplayer.com/walkthrough/ripple-click-effect-google-material-design       
            var element, circle, d, x, y;
            $('body').on('click', 'a.btn, button.btn, input.btn, label.btn', function(e) { 
                element = $(this);
      
                if(element.find(".md-click-circle").length == 0) {
                    element.prepend("<span class='md-click-circle'></span>");
                }
                    
                circle = element.find(".md-click-circle");
                circle.removeClass("md-click-animate");
                
                if(!circle.height() && !circle.width()) {
                    d = Math.max(element.outerWidth(), element.outerHeight());
                    circle.css({height: d, width: d});
                }
                
                x = e.pageX - element.offset().left - circle.width()/2;
                y = e.pageY - element.offset().top - circle.height()/2;
                
                circle.css({top: y+'px', left: x+'px'}).addClass("md-click-animate");

                setTimeout(function() {
                    circle.remove();      
                }, 1000);
            });
        }

        // Floating labels
        var handleInput = function(el) {
            if (el.val() != "") {
                el.addClass('edited');
            } else {
                el.removeClass('edited');
            }
        } 

        $('body').on('keydown', '.form-md-floating-label .form-control', function(e) { 
            handleInput($(this));
        });
        $('body').on('blur', '.form-md-floating-label .form-control', function(e) { 
            handleInput($(this));
        });        

        $('.form-md-floating-label .form-control').each(function(){
            if ($(this).val().length > 0) {
                $(this).addClass('edited');
            }
        });
    }

    // Handles custom checkboxes & radios using jQuery iCheck plugin
    var handleiCheck = function() {
        if (!$().iCheck) {
            return;
        }

        $('.icheck').each(function() {
            var checkboxClass = $(this).attr('data-checkbox') ? $(this).attr('data-checkbox') : 'icheckbox_minimal-grey';
            var radioClass = $(this).attr('data-radio') ? $(this).attr('data-radio') : 'iradio_minimal-grey';

            if (checkboxClass.indexOf('_line') > -1 || radioClass.indexOf('_line') > -1) {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass,
                    insert: '<div class="icheck_line-icon"></div>' + $(this).attr("data-label")
                });
            } else {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass
                });
            }
        });
    };

    // Handles Bootstrap switches
    var handleBootstrapSwitch = function() {
        if (!$().bootstrapSwitch) {
            return;
        }
        $('.make-switch').bootstrapSwitch();
    };

    // Handles Bootstrap confirmations
    var handleBootstrapConfirmation = function() {
        if (!$().confirmation) {
            return;
        }
        $('[data-toggle=confirmation]').confirmation({ container: 'body', btnOkClass: 'btn btn-sm btn-success', btnCancelClass: 'btn btn-sm btn-danger'});
    }
    
    // Handles Bootstrap Accordions.
    var handleAccordions = function() {
        $('body').on('shown.bs.collapse', '.accordion.scrollable', function(e) {
            App.scrollTo($(e.target));
        });
    };

    // Handles Bootstrap Tabs.
    var handleTabs = function() {
        //activate tab if tab id provided in the URL
        if (location.hash) {
            var tabid = encodeURI(location.hash.substr(1));
            $('a[href="#' + tabid + '"]').parents('.tab-pane:hidden').each(function() {
                var tabid = $(this).attr("id");
                $('a[href="#' + tabid + '"]').click();
            });
            $('a[href="#' + tabid + '"]').click();
        }

        if ($().tabdrop) {
            $('.tabbable-tabdrop .nav-pills, .tabbable-tabdrop .nav-tabs').tabdrop({
                text: '<i class="fa fa-ellipsis-v"></i>&nbsp;<i class="fa fa-angle-down"></i>'
            });
        }
    };

    // Handles Bootstrap Modals.
    var handleModals = function() {        
        // fix stackable modal issue: when 2 or more modals opened, closing one of modal will remove .modal-open class. 
        $('body').on('hide.bs.modal', function() {
            if ($('.modal:visible').size() > 1 && $('html').hasClass('modal-open') === false) {
                $('html').addClass('modal-open');
            } else if ($('.modal:visible').size() <= 1) {
                $('html').removeClass('modal-open');
            }
        });

        // fix page scrollbars issue
        $('body').on('show.bs.modal', '.modal', function() {
            if ($(this).hasClass("modal-scroll")) {
                $('body').addClass("modal-open-noscroll");
            }
        });

        // fix page scrollbars issue
        $('body').on('hide.bs.modal', '.modal', function() {
            $('body').removeClass("modal-open-noscroll");
        });

        // remove ajax content and remove cache on modal closed 
        $('body').on('hidden.bs.modal', '.modal:not(.modal-cached)', function () {
            $(this).removeData('bs.modal');
        });
    };

    // Handles Bootstrap Tooltips.
    var handleTooltips = function() {
        // global tooltips
        $('.tooltips').tooltip();

        // portlet tooltips
        $('.portlet > .portlet-title .fullscreen').tooltip({
            container: 'body',
            title: 'Fullscreen'
        });
        $('.portlet > .portlet-title > .tools > .reload').tooltip({
            container: 'body',
            title: 'Reload'
        });
        $('.portlet > .portlet-title > .tools > .remove').tooltip({
            container: 'body',
            title: 'Remove'
        });
        $('.portlet > .portlet-title > .tools > .config').tooltip({
            container: 'body',
            title: 'Settings'
        });
        $('.portlet > .portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip({
            container: 'body',
            title: 'Collapse/Expand'
        });
    };

    // Handles Bootstrap Dropdowns
    var handleDropdowns = function() {
        /*
          Hold dropdown on click  
        */
        $('body').on('click', '.dropdown-menu.hold-on-click', function(e) {
            e.stopPropagation();
        });
    };

    var handleAlerts = function() {
        $('body').on('click', '[data-close="alert"]', function(e) {
            $(this).parent('.alert').hide();
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-close="note"]', function(e) {
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-remove="note"]', function(e) {
            $(this).closest('.note').remove();
            e.preventDefault();
        });
    };

    // Handle Hower Dropdowns
    var handleDropdownHover = function() {
        $('[data-hover="dropdown"]').not('.hover-initialized').each(function() {
            $(this).dropdownHover();
            $(this).addClass('hover-initialized');
        });
    };

    // Handle textarea autosize 
    var handleTextareaAutosize = function() {
        if (typeof(autosize) == "function") {
            autosize(document.querySelector('textarea.autosizeme'));
        }
    }

    // Handles Bootstrap Popovers

    // last popep popover
    var lastPopedPopover;

    var handlePopovers = function() {
        $('.popovers').popover();

        // close last displayed popover

        $(document).on('click.bs.popover.data-api', function(e) {
            if (lastPopedPopover) {
                lastPopedPopover.popover('hide');
            }
        });
    };

    // Handles scrollable contents using jQuery SlimScroll plugin.
    var handleScrollers = function() {
        App.initSlimScroll('.scroller');
    };

    // Handles Image Preview using jQuery Fancybox plugin
    var handleFancybox = function() {
        if (!jQuery.fancybox) {
            return;
        }

        if ($(".fancybox-button").size() > 0) {
            $(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });
        }
    };

    // Handles counterup plugin wrapper
    var handleCounterup = function() {
        if (!$().counterUp) {
            return;
        }

        $("[data-counter='counterup']").counterUp({
            delay: 10,
            time: 1000
        });
    };

    // Fix input placeholder issue for IE8 and IE9
    var handleFixInputPlaceholderForIE = function() {
        //fix html5 placeholder attribute for ie7 & ie8
        if (isIE8 || isIE9) { // ie8 & ie9
            // this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
            $('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function() {
                var input = $(this);

                if (input.val() === '' && input.attr("placeholder") !== '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function() {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function() {
                    if (input.val() === '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    };

    // Handle Select2 Dropdowns
    var handleSelect2 = function() {
        if ($().select2) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $('.select2me').select2({
                placeholder: "Select",
                width: 'auto', 
                allowClear: true
            });
        }
    };

    // handle group element heights
   var handleHeight = function() {
       $('[data-auto-height]').each(function() {
            var parent = $(this);
            var items = $('[data-height]', parent);
            var height = 0;
            var mode = parent.attr('data-mode');
            var offset = parseInt(parent.attr('data-offset') ? parent.attr('data-offset') : 0);

            items.each(function() {
                if ($(this).attr('data-height') == "height") {
                    $(this).css('height', '');
                } else {
                    $(this).css('min-height', '');
                }

                var height_ = (mode == 'base-height' ? $(this).outerHeight() : $(this).outerHeight(true));
                if (height_ > height) {
                    height = height_;
                }
            });

            height = height + offset;

            items.each(function() {
                if ($(this).attr('data-height') == "height") {
                    $(this).css('height', height);
                } else {
                    $(this).css('min-height', height);
                }
            });

            if(parent.attr('data-related')) {
                $(parent.attr('data-related')).css('height', parent.height());
            }
       });       
    }
    
    //* END:CORE HANDLERS *//

    return {

        //main function to initiate the theme
        init: function() {
            //IMPORTANT!!!: Do not modify the core handlers call order.

            //Core handlers
            handleInit(); // initialize core variables
            handleOnResize(); // set and handle responsive    

            //UI Component handlers     
            handleMaterialDesign(); // handle material design       
            handleUniform(); // hanfle custom radio & checkboxes
            handleiCheck(); // handles custom icheck radio and checkboxes
            handleBootstrapSwitch(); // handle bootstrap switch plugin
            handleScrollers(); // handles slim scrolling contents 
            handleFancybox(); // handle fancy box
            handleSelect2(); // handle custom Select2 dropdowns
            handlePortletTools(); // handles portlet action bar functionality(refresh, configure, toggle, remove)
            handleAlerts(); //handle closabled alerts
            handleDropdowns(); // handle dropdowns
            handleTabs(); // handle tabs
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions 
            handleModals(); // handle modals
            handleBootstrapConfirmation(); // handle bootstrap confirmations
            handleTextareaAutosize(); // handle autosize textareas
            handleCounterup(); // handle counterup instances

            //Handle group element heights
            this.addResizeHandler(handleHeight); // handle auto calculating height on window resize

            // Hacks
            handleFixInputPlaceholderForIE(); //IE8 & IE9 input placeholder issue fix
        },

        //main function to initiate core javascript after ajax complete
        initAjax: function() {
            handleUniform(); // handles custom radio & checkboxes     
            handleiCheck(); // handles custom icheck radio and checkboxes
            handleBootstrapSwitch(); // handle bootstrap switch plugin
            handleDropdownHover(); // handles dropdown hover       
            handleScrollers(); // handles slim scrolling contents 
            handleSelect2(); // handle custom Select2 dropdowns
            handleFancybox(); // handle fancy box
            handleDropdowns(); // handle dropdowns
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions 
            handleBootstrapConfirmation(); // handle bootstrap confirmations
        },

        //init main components 
        initComponents: function() {
            this.initAjax();
        },

        //public function to remember last opened popover that needs to be closed on click
        setLastPopedPopover: function(el) {
            lastPopedPopover = el;
        },

        //public function to add callback a function which will be called on window resize
        addResizeHandler: function(func) {
            resizeHandlers.push(func);
        },

        //public functon to call _runresizeHandlers
        runResizeHandlers: function() {
            _runResizeHandlers();
        },

        // wrApper function to scroll(focus) to an element
        scrollTo: function(el, offeset) {
            var pos = (el && el.size() > 0) ? el.offset().top : 0;

            if (el) {
                if ($('body').hasClass('page-header-fixed')) {
                    pos = pos - $('.page-header').height();
                } else if ($('body').hasClass('page-header-top-fixed')) {
                    pos = pos - $('.page-header-top').height();
                } else if ($('body').hasClass('page-header-menu-fixed')) {
                    pos = pos - $('.page-header-menu').height();
                }
                pos = pos + (offeset ? offeset : -1 * el.height());
            }

            $('html,body').animate({
                scrollTop: pos
            }, 'slow');
        },

        initSlimScroll: function(el) {
            $(el).each(function() {
                if ($(this).attr("data-initialized")) {
                    return; // exit
                }

                var height;

                if ($(this).attr("data-height")) {
                    height = $(this).attr("data-height");
                } else {
                    height = $(this).css('height');
                }

                $(this).slimScroll({
                    allowPageScroll: true, // allow page scroll when the element scroll is ended
                    size: '7px',
                    color: ($(this).attr("data-handle-color") ? $(this).attr("data-handle-color") : '#bbb'),
                    wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                    railColor: ($(this).attr("data-rail-color") ? $(this).attr("data-rail-color") : '#eaeaea'),
                    position: isRTL ? 'left' : 'right',
                    height: height,
                    alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
                    railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
                    disableFadeOut: true
                });

                $(this).attr("data-initialized", "1");
            });
        },

        destroySlimScroll: function(el) {
            $(el).each(function() {
                if ($(this).attr("data-initialized") === "1") { // destroy existing instance before updating the height
                    $(this).removeAttr("data-initialized");
                    $(this).removeAttr("style");

                    var attrList = {};

                    // store the custom attribures so later we will reassign.
                    if ($(this).attr("data-handle-color")) {
                        attrList["data-handle-color"] = $(this).attr("data-handle-color");
                    }
                    if ($(this).attr("data-wrapper-class")) {
                        attrList["data-wrapper-class"] = $(this).attr("data-wrapper-class");
                    }
                    if ($(this).attr("data-rail-color")) {
                        attrList["data-rail-color"] = $(this).attr("data-rail-color");
                    }
                    if ($(this).attr("data-always-visible")) {
                        attrList["data-always-visible"] = $(this).attr("data-always-visible");
                    }
                    if ($(this).attr("data-rail-visible")) {
                        attrList["data-rail-visible"] = $(this).attr("data-rail-visible");
                    }

                    $(this).slimScroll({
                        wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                        destroy: true
                    });

                    var the = $(this);

                    // reassign custom attributes
                    $.each(attrList, function(key, value) {
                        the.attr(key, value);
                    });

                }
            });
        },

        // function to scroll to the top
        scrollTop: function() {
            App.scrollTo();
        },

        // wrApper function to  block element(indicate loading)
        blockUI: function(options) {
            options = $.extend(true, {}, options);
            var html = '';
            if (options.animate) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '">' + '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' + '</div>';
            } else if (options.iconOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif" align=""></div>';
            } else if (options.textOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
            } else {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif" align=""><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
            }

            if (options.target) { // element blocking
                var el = $(options.target);
                if (el.height() <= ($(window).height())) {
                    options.cenrerY = true;
                }
                el.block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                    css: {
                        top: '10%',
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            } else { // page blocking
                $.blockUI({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    css: {
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            }
        },

        // wrApper function to  un-block element(finish loading)
        unblockUI: function(target) {
            if (target) {
                $(target).unblock({
                    onUnblock: function() {
                        $(target).css('position', '');
                        $(target).css('zoom', '');
                    }
                });
            } else {
                $.unblockUI();
            }
        },

        startPageLoading: function(options) {
            if (options && options.animate) {
                $('.page-spinner-bar').remove();
                $('body').append('<div class="page-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
            } else {
                $('.page-loading').remove();
                $('body').append('<div class="page-loading"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>' + (options && options.message ? options.message : 'Loading...') + '</span></div>');
            }
        },

        stopPageLoading: function() {
            $('.page-loading, .page-spinner-bar').remove();
        },

        alert: function(options) {

            options = $.extend(true, {
                container: "", // alerts parent container(by default placed after the page breadcrumbs)
                place: "append", // "append" or "prepend" in container 
                type: 'success', // alert's type
                message: "", // alert's message
                close: true, // make alert closable
                reset: true, // close all previouse alerts first
                focus: true, // auto scroll to the alert after shown
                closeInSeconds: 0, // auto close after defined seconds
                icon: "" // put icon before the message
            }, options);

            var id = App.getUniqueID("App_alert");

            var html = '<div id="' + id + '" class="custom-alerts alert alert-' + options.type + ' fade in">' + (options.close ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' : '') + (options.icon !== "" ? '<i class="fa-lg fa fa-' + options.icon + '"></i>  ' : '') + options.message + '</div>';

            if (options.reset) {
                $('.custom-alerts').remove();
            }

            if (!options.container) {
                if ($('.page-fixed-main-content').size() === 1) {
                    $('.page-fixed-main-content').prepend(html);
                } else if (($('body').hasClass("page-container-bg-solid") || $('body').hasClass("page-content-white")) && $('.page-head').size() === 0) {
                    $('.page-title').after(html);
                } else {
                    if ($('.page-bar').size() > 0) {
                        $('.page-bar').after(html);
                    } else {
                        $('.page-breadcrumb, .breadcrumbs').after(html);
                    }
                }
            } else {
                if (options.place == "append") {
                    $(options.container).append(html);
                } else {
                    $(options.container).prepend(html);
                }
            }

            if (options.focus) {
                App.scrollTo($('#' + id));
            }

            if (options.closeInSeconds > 0) {
                setTimeout(function() {
                    $('#' + id).remove();
                }, options.closeInSeconds * 1000);
            }

            return id;
        },

        // initializes uniform elements
        initUniform: function(els) {
            if (els) {
                $(els).each(function() {
                    if ($(this).parents(".checker").size() === 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            } else {
                handleUniform();
            }
        },

        //wrApper function to update/sync jquery uniform checkbox & radios
        updateUniform: function(els) {
            $.uniform.update(els); // update the uniform checkbox & radios UI after the actual input control state changed
        },

        //public function to initialize the fancybox plugin
        initFancybox: function() {
            handleFancybox();
        },

        //public helper function to get actual input value(used in IE9 and IE8 due to placeholder attribute not supported)
        getActualVal: function(el) {
            el = $(el);
            if (el.val() === el.attr("placeholder")) {
                return "";
            }
            return el.val();
        },

        //public function to get a paremeter by name from URL
        getURLParameter: function(paramName) {
            var searchString = window.location.search.substring(1),
                i, val, params = searchString.split("&");

            for (i = 0; i < params.length; i++) {
                val = params[i].split("=");
                if (val[0] == paramName) {
                    return unescape(val[1]);
                }
            }
            return null;
        },

        // check for device touch support
        isTouchDevice: function() {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        },

        // To get the correct viewport width based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
        getViewPort: function() {
            var e = window,
                a = 'inner';
            if (!('innerWidth' in window)) {
                a = 'client';
                e = document.documentElement || document.body;
            }

            return {
                width: e[a + 'Width'],
                height: e[a + 'Height']
            };
        },

        getUniqueID: function(prefix) {
            return 'prefix_' + Math.floor(Math.random() * (new Date()).getTime());
        },

        // check IE8 mode
        isIE8: function() {
            return isIE8;
        },

        // check IE9 mode
        isIE9: function() {
            return isIE9;
        },

        //check RTL mode
        isRTL: function() {
            return isRTL;
        },

        // check IE8 mode
        isAngularJsApp: function() {
            return (typeof angular == 'undefined') ? false : true;
        },

        getAssetsPath: function() {
            return assetsPath;
        },

        setAssetsPath: function(path) {
            assetsPath = path;
        },

        setGlobalImgPath: function(path) {
            globalImgPath = path;
        },

        getGlobalImgPath: function() {
            return assetsPath + globalImgPath;
        },

        setGlobalPluginsPath: function(path) {
            globalPluginsPath = path;
        },

        getGlobalPluginsPath: function() {
            return assetsPath + globalPluginsPath;
        },

        getGlobalCssPath: function() {
            return assetsPath + globalCssPath;
        },

        // get layout color code by color name
        getBrandColor: function(name) {
            if (brandColors[name]) {
                return brandColors[name];
            } else {
                return '';
            }
        },

        getResponsiveBreakpoint: function(size) {
            // bootstrap responsive breakpoints
            var sizes = {
                'xs' : 480,     // extra small
                'sm' : 768,     // small
                'md' : 992,     // medium
                'lg' : 1200     // large
            };

            return sizes[size] ? sizes[size] : 0; 
        }
    };

}();

jQuery(document).ready(function() {    
   App.init(); // init metronic core componets
});
var Profile = function() {

    var dashboardMainChart = null;

    return {

        //main function
        init: function() {
            Profile.initMiniCharts();
        },

        initMiniCharts: function() {

            // IE8 Fix: function.bind polyfill
            if (App.isIE8() && !Function.prototype.bind) {
                Function.prototype.bind = function(oThis) {
                    if (typeof this !== "function") {
                        // closest thing possible to the ECMAScript 5 internal IsCallable function
                        throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
                    }

                    var aArgs = Array.prototype.slice.call(arguments, 1),
                        fToBind = this,
                        fNOP = function() {},
                        fBound = function() {
                            return fToBind.apply(this instanceof fNOP && oThis ? this : oThis,
                                aArgs.concat(Array.prototype.slice.call(arguments)));
                        };

                    fNOP.prototype = this.prototype;
                    fBound.prototype = new fNOP();

                    return fBound;
                };
            }

            $("#sparkline_bar").sparkline([8, 9, 10, 11, 10, 10, 12, 10, 10, 11, 9, 12, 11], {
                type: 'bar',
                width: '100',
                barWidth: 6,
                height: '45',
                barColor: '#F36A5B',
                negBarColor: '#e02222'
            });

            $("#sparkline_bar2").sparkline([9, 11, 12, 13, 12, 13, 10, 14, 13, 11, 11, 12, 11], {
                type: 'bar',
                width: '100',
                barWidth: 6,
                height: '45',
                barColor: '#5C9BD1',
                negBarColor: '#e02222'
            });
        }

    };

}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {
        Profile.init();
    });
}
var FormInputMask = function () {
    
    var handleInputMasks = function () {
        

        $("#mask_date").inputmask("d/m/y", {
            autoUnmask: true
        }); //direct mask        
        $("#mask_date1").inputmask("d/m/y", {
            "placeholder": "*"
        }); //change the placeholder
        $("#mask_date2").inputmask("d/m/y", {
            "placeholder": "dd/mm/yyyy"
        }); //multi-char placeholder
        $("#mask_phone").inputmask("mask", {
            "mask": "(999) 999-9999"
        }); //specifying fn & options
        $("#mask_tin").inputmask({
            "mask": "99-9999999",
            placeholder: "" // remove underscores from the input mask
        }); //specifying options only
        $(".mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        }); // ~ mask "9" or mask "99" or ... mask "9999999999"
        $(".mask_decimal").inputmask('decimal', {
            rightAlignNumerics: false
        }); //disables the right alignment of the decimal input
        $(".mask_currency_rl").inputmask('€ 999.999.999,99', {
            numericInput: true
        }); //123456  =>  € ___.__1.234,56

        $(".mask_currency_ll").inputmask('$ 999,999,999.99', {
            numericInput: true,
            rightAlignNumerics: false,
            greedy: false
        }); //123456  =>  € ___.__1.234,56
        $("#mask_ssn").inputmask("999-99-9999", {
            placeholder: " ",
            clearMaskOnLostFocus: true
        }); //default
    };

    return {
        //main function to initiate the module
        init: function () {
            handleInputMasks();
        }
    };

}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {
        FormInputMask.init(); // init metronic core componets
    });
}
var ComponentsBootstrapSelect=function(){var n=function(){$(".bs-select").selectpicker({iconBase:"fa",tickIcon:"fa-check"})};return{init:function(){n()}}}();App.isAngularJsApp()===!1&&jQuery(document).ready(function(){ComponentsBootstrapSelect.init()});
var Layout=function(){var e="layouts/layout/img/",a="layouts/layout/css/",t=App.getResponsiveBreakpoint("md"),i=function(){var e,a=$(".page-content"),i=$(".page-sidebar"),s=$("body");if(s.hasClass("page-footer-fixed")===!0&&s.hasClass("page-sidebar-fixed")===!1){var o=App.getViewPort().height-$(".page-footer").outerHeight()-$(".page-header").outerHeight();a.height()<o&&a.attr("style","min-height:"+o+"px")}else{if(s.hasClass("page-sidebar-fixed"))e=n(),s.hasClass("page-footer-fixed")===!1&&(e-=$(".page-footer").outerHeight());else{var r=$(".page-header").outerHeight(),d=$(".page-footer").outerHeight();e=App.getViewPort().width<t?App.getViewPort().height-r-d:i.height()+20,e+r+d<=App.getViewPort().height&&(e=App.getViewPort().height-r-d)}a.attr("style","min-height:"+e+"px")}},s=function(e,a){var i=location.hash.toLowerCase(),s=$(".page-sidebar-menu");if("click"===e||"set"===e?a=$(a):"match"===e&&s.find("li > a").each(function(){var e=$(this).attr("href").toLowerCase();return e.length>1&&i.substr(1,e.length-1)==e.substr(1)?void(a=$(this)):void 0}),a&&0!=a.size()&&"javascript:;"!==a.attr("href").toLowerCase()&&"#"!==a.attr("href").toLowerCase()){parseInt(s.data("slide-speed")),s.data("keep-expanded");s.hasClass("page-sidebar-menu-hover-submenu")===!1?s.find("li.nav-item.open").each(function(){var e=!1;$(this).find("li").each(function(){return $(this).find(" > a").attr("href")===a.attr("href")?void(e=!0):void 0}),e!==!0&&($(this).removeClass("open"),$(this).find("> a > .arrow.open").removeClass("open"),$(this).find("> .sub-menu").slideUp())}):s.find("li.open").removeClass("open"),s.find("li.active").removeClass("active"),s.find("li > a > .selected").remove(),a.parents("li").each(function(){$(this).addClass("active"),$(this).find("> a > span.arrow").addClass("open"),1===$(this).parent("ul.page-sidebar-menu").size()&&$(this).find("> a").append('<span class="selected"></span>'),1===$(this).children("ul.sub-menu").size()&&$(this).addClass("open")}),"click"===e&&App.getViewPort().width<t&&$(".page-sidebar").hasClass("in")&&$(".page-header .responsive-toggler").click()}},o=function(){$(".page-sidebar-menu").on("click","li > a.nav-toggle, li > a > span.nav-toggle",function(e){var a=$(this).closest(".nav-item").children(".nav-link");if(!(App.getViewPort().width>=t&&!$(".page-sidebar-menu").attr("data-initialized")&&$("body").hasClass("page-sidebar-closed")&&1===a.parent("li").parent(".page-sidebar-menu").size())){var s=a.next().hasClass("sub-menu");if(!(App.getViewPort().width>=t&&1===a.parents(".page-sidebar-menu-hover-submenu").size())){if(s===!1)return void(App.getViewPort().width<t&&$(".page-sidebar").hasClass("in")&&$(".page-header .responsive-toggler").click());var o=a.parent().parent(),n=a,r=$(".page-sidebar-menu"),d=a.next(),p=r.data("auto-scroll"),l=parseInt(r.data("slide-speed")),c=r.data("keep-expanded");c||(o.children("li.open").children("a").children(".arrow").removeClass("open"),o.children("li.open").children(".sub-menu:not(.always-open)").slideUp(l),o.children("li.open").removeClass("open"));var h=-200;d.is(":visible")?($(".arrow",n).removeClass("open"),n.parent().removeClass("open"),d.slideUp(l,function(){p===!0&&$("body").hasClass("page-sidebar-closed")===!1&&($("body").hasClass("page-sidebar-fixed")?r.slimScroll({scrollTo:n.position().top}):App.scrollTo(n,h)),i()})):s&&($(".arrow",n).addClass("open"),n.parent().addClass("open"),d.slideDown(l,function(){p===!0&&$("body").hasClass("page-sidebar-closed")===!1&&($("body").hasClass("page-sidebar-fixed")?r.slimScroll({scrollTo:n.position().top}):App.scrollTo(n,h)),i()})),e.preventDefault()}}}),App.isAngularJsApp()&&$(".page-sidebar-menu li > a").on("click",function(e){App.getViewPort().width<t&&$(this).next().hasClass("sub-menu")===!1&&$(".page-header .responsive-toggler").click()}),$(".page-sidebar").on("click"," li > a.ajaxify",function(e){e.preventDefault(),App.scrollTop();var a=$(this).attr("href"),i=$(".page-sidebar ul"),s=($(".page-content"),$(".page-content .page-content-body"));i.children("li.active").removeClass("active"),i.children("arrow.open").removeClass("open"),$(this).parents("li").each(function(){$(this).addClass("active"),$(this).children("a > span.arrow").addClass("open")}),$(this).parents("li").addClass("active"),App.getViewPort().width<t&&$(".page-sidebar").hasClass("in")&&$(".page-header .responsive-toggler").click(),App.startPageLoading();var o=$(this);$.ajax({type:"GET",cache:!1,url:a,dataType:"html",success:function(e){0===o.parents("li.open").size()&&$(".page-sidebar-menu > li.open > a").click(),App.stopPageLoading(),s.html(e),Layout.fixContentHeight(),App.initAjax()},error:function(e,a,t){App.stopPageLoading(),s.html("<h4>Could not load the requested content.</h4>")}})}),$(".page-content").on("click",".ajaxify",function(e){e.preventDefault(),App.scrollTop();var a=$(this).attr("href"),i=($(".page-content"),$(".page-content .page-content-body"));App.startPageLoading(),App.getViewPort().width<t&&$(".page-sidebar").hasClass("in")&&$(".page-header .responsive-toggler").click(),$.ajax({type:"GET",cache:!1,url:a,dataType:"html",success:function(e){App.stopPageLoading(),i.html(e),Layout.fixContentHeight(),App.initAjax()},error:function(e,a,t){i.html("<h4>Could not load the requested content.</h4>"),App.stopPageLoading()}})}),$(document).on("click",".page-header-fixed-mobile .page-header .responsive-toggler",function(){App.scrollTop()}),d(),$(".page-sidebar").on("click",".sidebar-search .remove",function(e){e.preventDefault(),$(".sidebar-search").removeClass("open")}),$(".page-sidebar .sidebar-search").on("keypress","input.form-control",function(e){return 13==e.which?($(".sidebar-search").submit(),!1):void 0}),$(".sidebar-search .submit").on("click",function(e){e.preventDefault(),$("body").hasClass("page-sidebar-closed")&&$(".sidebar-search").hasClass("open")===!1?(1===$(".page-sidebar-fixed").size()&&$(".page-sidebar .sidebar-toggler").click(),$(".sidebar-search").addClass("open")):$(".sidebar-search").submit()}),0!==$(".sidebar-search").size()&&($(".sidebar-search .input-group").on("click",function(e){e.stopPropagation()}),$("body").on("click",function(){$(".sidebar-search").hasClass("open")&&$(".sidebar-search").removeClass("open")}))},n=function(){var e=App.getViewPort().height-$(".page-header").outerHeight(!0);return $("body").hasClass("page-footer-fixed")&&(e-=$(".page-footer").outerHeight()),e},r=function(){var e=$(".page-sidebar-menu");return App.destroySlimScroll(e),0===$(".page-sidebar-fixed").size()?void i():void(App.getViewPort().width>=t&&(e.attr("data-height",n()),App.initSlimScroll(e),i()))},d=function(){var e=$("body");e.hasClass("page-sidebar-fixed")&&$(".page-sidebar").on("mouseenter",function(){e.hasClass("page-sidebar-closed")&&$(this).find(".page-sidebar-menu").removeClass("page-sidebar-menu-closed")}).on("mouseleave",function(){e.hasClass("page-sidebar-closed")&&$(this).find(".page-sidebar-menu").addClass("page-sidebar-menu-closed")})},p=function(){var e=$("body");$.cookie&&"1"===$.cookie("sidebar_closed")&&App.getViewPort().width>=t&&($("body").addClass("page-sidebar-closed"),$(".page-sidebar-menu").addClass("page-sidebar-menu-closed")),$("body").on("click",".sidebar-toggler",function(a){var t=$(".page-sidebar"),i=$(".page-sidebar-menu");$(".sidebar-search",t).removeClass("open"),e.hasClass("page-sidebar-closed")?(e.removeClass("page-sidebar-closed"),i.removeClass("page-sidebar-menu-closed"),$.cookie&&$.cookie("sidebar_closed","0")):(e.addClass("page-sidebar-closed"),i.addClass("page-sidebar-menu-closed"),e.hasClass("page-sidebar-fixed")&&i.trigger("mouseleave"),$.cookie&&$.cookie("sidebar_closed","1")),$(window).trigger("resize")})},l=function(){$(".page-header").on("click",'.hor-menu a[data-toggle="tab"]',function(e){e.preventDefault();var a=$(".hor-menu .nav"),t=a.find("li.current");$("li.active",t).removeClass("active"),$(".selected",t).remove();var i=$(this).parents("li").last();i.addClass("current"),i.find("a:first").append('<span class="selected"></span>')}),$(".page-header").on("click",".search-form",function(e){$(this).addClass("open"),$(this).find(".form-control").focus(),$(".page-header .search-form .form-control").on("blur",function(e){$(this).closest(".search-form").removeClass("open"),$(this).unbind("blur")})}),$(".page-header").on("keypress",".hor-menu .search-form .form-control",function(e){return 13==e.which?($(this).closest(".search-form").submit(),!1):void 0}),$(".page-header").on("mousedown",".search-form.open .submit",function(e){e.preventDefault(),e.stopPropagation(),$(this).closest(".search-form").submit()}),$('[data-hover="megamenu-dropdown"]').not(".hover-initialized").each(function(){$(this).dropdownHover(),$(this).addClass("hover-initialized")}),$(document).on("click",".mega-menu-dropdown .dropdown-menu",function(e){e.stopPropagation()})},c=function(){$("body").on("shown.bs.tab",'a[data-toggle="tab"]',function(){i()})},h=function(){var e=300,a=500;navigator.userAgent.match(/iPhone|iPad|iPod/i)?$(window).bind("touchend touchcancel touchleave",function(t){$(this).scrollTop()>e?$(".scroll-to-top").fadeIn(a):$(".scroll-to-top").fadeOut(a)}):$(window).scroll(function(){$(this).scrollTop()>e?$(".scroll-to-top").fadeIn(a):$(".scroll-to-top").fadeOut(a)}),$(".scroll-to-top").click(function(e){return e.preventDefault(),$("html, body").animate({scrollTop:0},a),!1})},g=function(){$(".full-height-content").each(function(){var e,a=$(this);if(e=App.getViewPort().height-$(".page-header").outerHeight(!0)-$(".page-footer").outerHeight(!0)-$(".page-title").outerHeight(!0)-$(".page-bar").outerHeight(!0),a.hasClass("portlet")){var i=a.find(".portlet-body");App.destroySlimScroll(i.find(".full-height-content-body")),e=e-a.find(".portlet-title").outerHeight(!0)-parseInt(a.find(".portlet-body").css("padding-top"))-parseInt(a.find(".portlet-body").css("padding-bottom"))-5,App.getViewPort().width>=t&&a.hasClass("full-height-content-scrollable")?(e-=35,i.find(".full-height-content-body").css("height",e),App.initSlimScroll(i.find(".full-height-content-body"))):i.css("min-height",e)}else App.destroySlimScroll(a.find(".full-height-content-body")),App.getViewPort().width>=t&&a.hasClass("full-height-content-scrollable")?(e-=35,a.find(".full-height-content-body").css("height",e),App.initSlimScroll(a.find(".full-height-content-body"))):a.css("min-height",e)})};return{initHeader:function(){l()},setSidebarMenuActiveLink:function(e,a){s(e,a)},initSidebar:function(){r(),o(),p(),App.isAngularJsApp()&&s("match"),App.addResizeHandler(r)},initContent:function(){g(),c(),App.addResizeHandler(i),App.addResizeHandler(g)},initFooter:function(){h()},init:function(){this.initHeader(),this.initSidebar(),this.initContent(),this.initFooter()},fixContentHeight:function(){i()},initFixedSidebarHoverEffect:function(){d()},initFixedSidebar:function(){r()},getLayoutImgPath:function(){return App.getAssetsPath()+e},getLayoutCssPath:function(){return App.getAssetsPath()+a}}}();App.isAngularJsApp()===!1&&jQuery(document).ready(function(){Layout.init()});
var Demo=function(){var e=function(){var e=$(".theme-panel");$("body").hasClass("page-boxed")===!1&&$(".layout-option",e).val("fluid"),$(".sidebar-option",e).val("default"),$(".page-header-option",e).val("fixed"),$(".page-footer-option",e).val("default"),$(".sidebar-pos-option").attr("disabled")===!1&&$(".sidebar-pos-option",e).val(App.isRTL()?"right":"left");var a=function(){$("body").removeClass("page-boxed").removeClass("page-footer-fixed").removeClass("page-sidebar-fixed").removeClass("page-header-fixed").removeClass("page-sidebar-reversed"),$(".page-header > .page-header-inner").removeClass("container"),1===$(".page-container").parent(".container").size()&&$(".page-container").insertAfter("body > .clearfix"),1===$(".page-footer > .container").size()?$(".page-footer").html($(".page-footer > .container").html()):1===$(".page-footer").parent(".container").size()&&($(".page-footer").insertAfter(".page-container"),$(".scroll-to-top").insertAfter(".page-footer")),$(".top-menu > .navbar-nav > li.dropdown").removeClass("dropdown-dark"),$("body > .container").remove()},o="",t=function(){var t=$(".layout-option",e).val(),i=$(".sidebar-option",e).val(),d=$(".page-header-option",e).val(),r=$(".page-footer-option",e).val(),s=$(".sidebar-pos-option",e).val(),n=$(".sidebar-style-option",e).val(),p=$(".sidebar-menu-option",e).val(),l=$(".page-header-top-dropdown-style-option",e).val();if("fixed"==i&&"default"==d&&(alert("Default Header with Fixed Sidebar option is not supported. Proceed with Fixed Header with Fixed Sidebar."),$(".page-header-option",e).val("fixed"),$(".sidebar-option",e).val("fixed"),i="fixed",d="fixed"),a(),"boxed"===t){$("body").addClass("page-boxed"),$(".page-header > .page-header-inner").addClass("container");$("body > .clearfix").after('<div class="container"></div>');$(".page-container").appendTo("body > .container"),"fixed"===r?$(".page-footer").html('<div class="container">'+$(".page-footer").html()+"</div>"):$(".page-footer").appendTo("body > .container")}o!=t&&App.runResizeHandlers(),o=t,"fixed"===d?($("body").addClass("page-header-fixed"),$(".page-header").removeClass("navbar-static-top").addClass("navbar-fixed-top")):($("body").removeClass("page-header-fixed"),$(".page-header").removeClass("navbar-fixed-top").addClass("navbar-static-top")),$("body").hasClass("page-full-width")===!1&&("fixed"===i?($("body").addClass("page-sidebar-fixed"),$("page-sidebar-menu").addClass("page-sidebar-menu-fixed"),$("page-sidebar-menu").removeClass("page-sidebar-menu-default"),Layout.initFixedSidebarHoverEffect()):($("body").removeClass("page-sidebar-fixed"),$("page-sidebar-menu").addClass("page-sidebar-menu-default"),$("page-sidebar-menu").removeClass("page-sidebar-menu-fixed"),$(".page-sidebar-menu").unbind("mouseenter").unbind("mouseleave"))),"dark"===l?$(".top-menu > .navbar-nav > li.dropdown").addClass("dropdown-dark"):$(".top-menu > .navbar-nav > li.dropdown").removeClass("dropdown-dark"),"fixed"===r?$("body").addClass("page-footer-fixed"):$("body").removeClass("page-footer-fixed"),"light"===n?$(".page-sidebar-menu").addClass("page-sidebar-menu-light"):$(".page-sidebar-menu").removeClass("page-sidebar-menu-light"),"hover"===p?"fixed"==i?($(".sidebar-menu-option",e).val("accordion"),alert("Hover Sidebar Menu is not compatible with Fixed Sidebar Mode. Select Default Sidebar Mode Instead.")):$(".page-sidebar-menu").addClass("page-sidebar-menu-hover-submenu"):$(".page-sidebar-menu").removeClass("page-sidebar-menu-hover-submenu"),App.isRTL()?"left"===s?($("body").addClass("page-sidebar-reversed"),$("#frontend-link").tooltip("destroy").tooltip({placement:"right"})):($("body").removeClass("page-sidebar-reversed"),$("#frontend-link").tooltip("destroy").tooltip({placement:"left"})):"right"===s?($("body").addClass("page-sidebar-reversed"),$("#frontend-link").tooltip("destroy").tooltip({placement:"left"})):($("body").removeClass("page-sidebar-reversed"),$("#frontend-link").tooltip("destroy").tooltip({placement:"right"})),Layout.fixContentHeight(),Layout.initFixedSidebar()},i=function(e){var a=App.isRTL()?e+"-rtl":e;$("#style_color").attr("href",Layout.getLayoutCssPath()+"themes/"+a+".min.css"),"light2"==e?$(".page-logo img").attr("src",Layout.getLayoutImgPath()+"logo-invert.png"):$(".page-logo img").attr("src",Layout.getLayoutImgPath()+"logo.png")};$(".toggler",e).click(function(){$(".toggler").hide(),$(".toggler-close").show(),$(".theme-panel > .theme-options").show()}),$(".toggler-close",e).click(function(){$(".toggler").show(),$(".toggler-close").hide(),$(".theme-panel > .theme-options").hide()}),$(".theme-colors > ul > li",e).click(function(){var a=$(this).attr("data-style");i(a),$("ul > li",e).removeClass("current"),$(this).addClass("current")}),$("body").hasClass("page-boxed")&&$(".layout-option",e).val("boxed"),$("body").hasClass("page-sidebar-fixed")&&$(".sidebar-option",e).val("fixed"),$("body").hasClass("page-header-fixed")&&$(".page-header-option",e).val("fixed"),$("body").hasClass("page-footer-fixed")&&$(".page-footer-option",e).val("fixed"),$("body").hasClass("page-sidebar-reversed")&&$(".sidebar-pos-option",e).val("right"),$(".page-sidebar-menu").hasClass("page-sidebar-menu-light")&&$(".sidebar-style-option",e).val("light"),$(".page-sidebar-menu").hasClass("page-sidebar-menu-hover-submenu")&&$(".sidebar-menu-option",e).val("hover");$(".sidebar-option",e).val(),$(".page-header-option",e).val(),$(".page-footer-option",e).val(),$(".sidebar-pos-option",e).val(),$(".sidebar-style-option",e).val(),$(".sidebar-menu-option",e).val();$(".layout-option, .page-header-option, .page-header-top-dropdown-style-option, .sidebar-option, .page-footer-option, .sidebar-pos-option, .sidebar-style-option, .sidebar-menu-option",e).change(t)},a=function(e){var a="rounded"===e?"components-rounded":"components";a=App.isRTL()?a+"-rtl":a,$("#style_components").attr("href",App.getGlobalCssPath()+a+".min.css"),"undefined"!=typeof Cookies&&Cookies.set("layout-style-option",e)};return{init:function(){e(),$(".theme-panel .layout-style-option").change(function(){a($(this).val())}),"undefined"!=typeof Cookies&&"rounded"===Cookies.get("layout-style-option")&&(a(Cookies.get("layout-style-option")),$(".theme-panel .layout-style-option").val(Cookies.get("layout-style-option")))}}}();App.isAngularJsApp()===!1&&jQuery(document).ready(function(){Demo.init()});
var CustomFunctions = function() {

    var handleUserUpdate = function() {

        $("#user-update").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                name: {
                    required: true
                },
                email: {
                    email: true,
                    required: true
                },
                password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                }
            },

            messages: {
                name: {
                    required: "Name is required."
                },
                email: {
                    required: "email is required."
                },
                password: {
                    required: "Password is required."
                },
                confirm_password: {
                    required: "Passwords should match."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('#user-update')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });
    };

    var handleUserImageUpdate = function() {
        $('#user-image-update').validate({
            rules: {
                image: {
                    required: true
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('#user-image-update')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                var data = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: data,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    statusCode: {
                        200: function(response) {
                            $(".profile-userpic img").attr('src', response.image);
                        },
                        400: function() {

                        }
                    }
                });
                //form.submit(); // form validation success, call ajax form submit

            }
        });
    };

    var handleUserCreate = function() {

        $('#user-create').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                name: {
                    required: true
                },
                email: {
                    email: true,
                    required: true
                }
            },

            messages: {
                name: {
                    required: "Name is required."
                },
                email: {
                    required: "email is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('#user-create')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $('#user-create input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#user-create').validate().form()) {
                    $('#user-create').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    };

    var handleUserPassword = function() {

        $('#user-password-update').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                current_password: {
                    required: true
                },
                new_password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('#user-create')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                //form.submit(); // form validation success, call ajax form submit

                var data = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: data,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    statusCode: {
                        200: function() {
                            location.reload();
                        },
                        400: function(response) {
                            $(form).find('.alert').find('span').html(response.responseJSON.error.message);
                            $(form).find('.alert').slideDown('slow');
                        }
                    }
                });

            }
        });
    };

    var handleTextareaResize = function() {
        autosize($('.autosizeme'));
    };

    var handleItemCreate = function()   {
        $("#item-create").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                title: {
                    required: true
                },
                platform: {
                    required: true
                },
                description: {
                    required: true
                },
                countries: {
                    required: true
                },
                item_type: {
                    required: true
                },
                quantity: {
                    required: true
                },
                price: {
                    required: true
                },
                purchase_type: {
                    required: true
                }
            },

            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#item-create')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });
    };

    var handleItemDelete = function() {
        $("#items_table").on('click', '.item-delete', function(){
            var $href = $(this).data('href');
            var $row = $(this).closest('tr').prev();
            var $row_details = $(this).closest('tr');

            bootbox.confirm({
                message: 'Are you sure you want to delete this item?',
                size: 'small',
                buttons: {
                    'cancel': {
                        label: 'Cancel'
                    },
                    'confirm': {
                        label: 'Delete',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
                        $.ajax({
                            url: $href,
                            type: 'DELETE',
                            dataType: 'json',
                            statusCode: {
                                200: function() {
                                    $row_details.remove();
                                    $row.remove();
                                },
                                400: function() {

                                }
                            }
                        })
                    }
                }
            });
        });
    };

    var handleDeleteConfirmation = function() {

    };

    var initTable = function() {
        var table = $('.data-table');

        // begin first table
        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered1 from _MAX_ total records)",
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },

            "lengthMenu": [
                [10, 50, 100, -1],
                [10, 50, 100, "All"] // change per page values here
            ],
            // set the initial value
            "pagingType": "bootstrap_full_number"

        });
    };

    var handleDatePickers = function () {

        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        /* Workaround to restrict daterange past date select: http://stackoverflow.com/questions/11933173/how-to-restrict-the-selectable-date-ranges-in-bootstrap-datepicker */
    };

    var handlePurchaseCreate = function() {
        $("#purchase-create").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                user: {
                    required: true
                },
                item: {
                    required: true
                }
            },

            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#purchase-create')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });
    };

    var handlePushSettingsCreate = function() {
        $("#push-android-settings").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                api_key: {
                    required: true
                }
            },

            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#push-android-settings')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $("#push-ios-settings").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                api_key: {
                    required: true
                }
            },

            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#push-android-settings')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });
    };

    var handlePushSend = function() {
        $("#android-push").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                reg_id: {
                    required: true
                },
                'push[title]': {
                    required: true
                },
                'push[message]': {
                    required: true
                }
            },
            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#android-push')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $("#ios-push").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                reg_id: {
                    required: true
                },
                'push[message]': {
                    required: true
                }
            },
            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#ios-push')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $("#bulk-push").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                reg_id: {
                    required: true
                },
                'push[title]': {
                    required: true
                },
                'push[message]': {
                    required: true
                }
            },
            invalidHandler: function() { //display error alert on form submit
                $('.alert-danger', $('#bulk-push')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });


    };

    var handleProjectCreate = function() {
        $("#project-create").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            rules: {
                name: {
                    required: true
                },
                link: {
                    url: true,
                    required: true
                },
                image: {
                    required: true
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('#project-create')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });
    };

    return {
        //main function to initiate the module
        init: function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            handleUserCreate();
            handleUserImageUpdate();
            handleUserUpdate();
            handleUserPassword();
            handleTextareaResize();
            handleItemCreate();
            handleItemDelete();
            handleDeleteConfirmation();
            initTable();
            handleDatePickers();
            handlePurchaseCreate();
            handlePushSettingsCreate();
            handlePushSend();
            handleProjectCreate();

            $('.forget-form').hide();

        }

    };

}();

jQuery(document).ready(function() {
    CustomFunctions.init();
});
//# sourceMappingURL=main.js.map
