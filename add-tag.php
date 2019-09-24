<?php
header('Content-Type: text/html; charset=UTF-8');
//SQL pieslēgšanās informācija
$db_server = "localhost";
$db_user = "baumuin_bauma";
$db_password = "{GIwlpQ<?3>g";
$db_database = "baumuin_battle";

//pieslēdzamies SQL serverim
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_database);
mysqli_set_charset($connection, "utf8");
session_start();

require_once 'get/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('get/client_credentials.json');
$client->setAccessType ("offline");
$client->setApprovalPrompt ("force");
$client->setIncludeGrantedScopes(true);
$client->addScope("https://www.googleapis.com/auth/photoslibrary.readonly");

$accessFile = 'get/accessToken.json';
$refreshFile = 'get/refreshToken.json';

if (file_exists($accessFile)) {
	$accessToken = json_decode(file_get_contents($accessFile), true);
	if(isset($accessToken["access_token"]))
		$accessToken = $accessToken["access_token"];
	// echo $accessToken;
	$client->setAccessToken($accessToken);
}
if ($client->isAccessTokenExpired() && file_exists($refreshFile)) {
	$refreshToken = json_decode(file_get_contents($refreshFile), true);
	$client->fetchAccessTokenWithRefreshToken($refreshToken);
	
	file_put_contents($accessFile, json_encode($client->getAccessToken()));
	file_put_contents($refreshFile, json_encode($client->getRefreshToken()));
}else{
	$authUrl = $client->createAuthUrl();
	echo $authUrl, "\n";
	// $code = rtrim(fgets(STDIN));
	$client->authenticate($code);
	
	file_put_contents($accessFile, json_encode($client->getAccessToken()));
	file_put_contents($refreshFile, json_encode($client->getRefreshToken()));
}

$recentlyAddedTags = array();
//saglabāsim datubāzē
if (isset($_POST['submit'])){
    foreach($_POST as $key => $value){
        $keyParts = explode("-", $key);
        $id = $keyParts[1];
        if($keyParts[0] == "tags"){
            $tags = $value;
            //ja viss ir, saglabājam
            if(
                isset($id) && 
                isset($tags) && 
                isset($img) && 
                $id !== "" && 
                $tags !== "" && 
                $img !== ""
            ){
                $tagArray = explode(",", $tags);
                foreach($tagArray as $tag){
                    $recentlyAddedTags[] = $tag;
                    // echo $id."; ".$tag."; ".$img."</br>";
                    $result = mysqli_query($connection, "insert into tags (img, img_id, tag) values('$img', '$id', '$tag')");
                }
            }
            
            //nodzēšam vērtības
            unset($id);
            unset($tags);
            unset($img);
        }else if($keyParts[0] == "img"){
            $img = $value;
        }
    }
}
$recentlyAddedTags = array_unique($recentlyAddedTags);

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Add Tags</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
    var tabindex = 1;
    $(document).keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
        }
    });
    // bootstrap-tagsinput.js file - add in local

    (function ($) {
      "use strict";

      var defaultOptions = {
        tagClass: function(item) {
          return 'label label-info';
        },
        itemValue: function(item) {
          return item ? item.toString() : item;
        },
        itemText: function(item) {
          return this.itemValue(item);
        },
        itemTitle: function(item) {
          return null;
        },
        freeInput: true,
        addOnBlur: true,
        maxTags: undefined,
        maxChars: undefined,
        confirmKeys: [13, 44],
        delimiter: ',',
        delimiterRegex: null,
        cancelConfirmKeysOnEmpty: true,
        onTagExists: function(item, $tag) {
          $tag.hide().fadeIn();
        },
        trimValue: false,
        allowDuplicates: false
      };

      /**
       * Constructor function
       */
      function TagsInput(element, options) {
        this.itemsArray = [];

        this.$element = $(element);
        this.$element.hide();

        this.isSelect = (element.tagName === 'SELECT');
        this.multiple = (this.isSelect && element.hasAttribute('multiple'));
        this.objectItems = options && options.itemValue;
        this.placeholderText = element.hasAttribute('placeholder') ? this.$element.attr('placeholder') : '';
        this.inputSize = Math.max(1, this.placeholderText.length);

        this.$container = $('<div class="bootstrap-tagsinput"></div>');
        this.$input = $('<input tabindex="' + (tabindex++) + '" type="text" placeholder="' + this.placeholderText + '"/>').appendTo(this.$container);

        this.$element.before(this.$container);

        this.build(options);
      }

      TagsInput.prototype = {
        constructor: TagsInput,

        /**
         * Adds the given item as a new tag. Pass true to dontPushVal to prevent
         * updating the elements val()
         */
        add: function(item, dontPushVal, options) {
          var self = this;

          if (self.options.maxTags && self.itemsArray.length >= self.options.maxTags)
            return;

          // Ignore falsey values, except false
          if (item !== false && !item)
            return;

          // Trim value
          if (typeof item === "string" && self.options.trimValue) {
            item = $.trim(item);
          }

          // Throw an error when trying to add an object while the itemValue option was not set
          if (typeof item === "object" && !self.objectItems)
            throw("Can't add objects when itemValue option is not set");

          // Ignore strings only containg whitespace
          if (item.toString().match(/^\s*$/))
            return;

          // If SELECT but not multiple, remove current tag
          if (self.isSelect && !self.multiple && self.itemsArray.length > 0)
            self.remove(self.itemsArray[0]);

          if (typeof item === "string" && this.$element[0].tagName === 'INPUT') {
            var delimiter = (self.options.delimiterRegex) ? self.options.delimiterRegex : self.options.delimiter;
            var items = item.split(delimiter);
            if (items.length > 1) {
              for (var i = 0; i < items.length; i++) {
                this.add(items[i], true);
              }

              if (!dontPushVal)
                self.pushVal();
              return;
            }
          }

          var itemValue = self.options.itemValue(item),
              itemText = self.options.itemText(item),
              tagClass = self.options.tagClass(item),
              itemTitle = self.options.itemTitle(item);

          // Ignore items allready added
          var existing = $.grep(self.itemsArray, function(item) { return self.options.itemValue(item) === itemValue; } )[0];
          if (existing && !self.options.allowDuplicates) {
            // Invoke onTagExists
            if (self.options.onTagExists) {
              var $existingTag = $(".tag", self.$container).filter(function() { return $(this).data("item") === existing; });
              self.options.onTagExists(item, $existingTag);
            }
            return;
          }

          // if length greater than limit
          if (self.items().toString().length + item.length + 1 > self.options.maxInputLength)
            return;

          // raise beforeItemAdd arg
          var beforeItemAddEvent = $.Event('beforeItemAdd', { item: item, cancel: false, options: options});
          self.$element.trigger(beforeItemAddEvent);
          if (beforeItemAddEvent.cancel)
            return;

          // register item in internal array and map
          self.itemsArray.push(item);

          // add a tag element

          var $tag = $('<span class="tag ' + htmlEncode(tagClass) + (itemTitle !== null ? ('" title="' + itemTitle) : '') + '">' + htmlEncode(itemText) + '<span data-role="remove"></span></span>');
          $tag.data('item', item);
          self.findInputWrapper().before($tag);
          $tag.after(' ');

          // add <option /> if item represents a value not present in one of the <select />'s options
          if (self.isSelect && !$('option[value="' + encodeURIComponent(itemValue) + '"]',self.$element)[0]) {
            var $option = $('<option selected>' + htmlEncode(itemText) + '</option>');
            $option.data('item', item);
            $option.attr('value', itemValue);
            self.$element.append($option);
          }

          if (!dontPushVal)
            self.pushVal();

          // Add class when reached maxTags
          if (self.options.maxTags === self.itemsArray.length || self.items().toString().length === self.options.maxInputLength)
            self.$container.addClass('bootstrap-tagsinput-max');

          self.$element.trigger($.Event('itemAdded', { item: item, options: options }));
        },

        /**
         * Removes the given item. Pass true to dontPushVal to prevent updating the
         * elements val()
         */
        remove: function(item, dontPushVal, options) {
          var self = this;

          if (self.objectItems) {
            if (typeof item === "object")
              item = $.grep(self.itemsArray, function(other) { return self.options.itemValue(other) ==  self.options.itemValue(item); } );
            else
              item = $.grep(self.itemsArray, function(other) { return self.options.itemValue(other) ==  item; } );

            item = item[item.length-1];
          }

          if (item) {
            var beforeItemRemoveEvent = $.Event('beforeItemRemove', { item: item, cancel: false, options: options });
            self.$element.trigger(beforeItemRemoveEvent);
            if (beforeItemRemoveEvent.cancel)
              return;

            $('.tag', self.$container).filter(function() { return $(this).data('item') === item; }).remove();
            $('option', self.$element).filter(function() { return $(this).data('item') === item; }).remove();
            if($.inArray(item, self.itemsArray) !== -1)
              self.itemsArray.splice($.inArray(item, self.itemsArray), 1);
          }

          if (!dontPushVal)
            self.pushVal();

          // Remove class when reached maxTags
          if (self.options.maxTags > self.itemsArray.length)
            self.$container.removeClass('bootstrap-tagsinput-max');

          self.$element.trigger($.Event('itemRemoved',  { item: item, options: options }));
        },

        /**
         * Removes all items
         */
        removeAll: function() {
          var self = this;

          $('.tag', self.$container).remove();
          $('option', self.$element).remove();

          while(self.itemsArray.length > 0)
            self.itemsArray.pop();

          self.pushVal();
        },

        /**
         * Refreshes the tags so they match the text/value of their corresponding
         * item.
         */
        refresh: function() {
          var self = this;
          $('.tag', self.$container).each(function() {
            var $tag = $(this),
                item = $tag.data('item'),
                itemValue = self.options.itemValue(item),
                itemText = self.options.itemText(item),
                tagClass = self.options.tagClass(item);

              // Update tag's class and inner text
              $tag.attr('class', null);
              $tag.addClass('tag ' + htmlEncode(tagClass));
              $tag.contents().filter(function() {
                return this.nodeType == 3;
              })[0].nodeValue = htmlEncode(itemText);

              if (self.isSelect) {
                var option = $('option', self.$element).filter(function() { return $(this).data('item') === item; });
                option.attr('value', itemValue);
              }
          });
        },

        /**
         * Returns the items added as tags
         */
        items: function() {
          return this.itemsArray;
        },

        /**
         * Assembly value by retrieving the value of each item, and set it on the
         * element.
         */
        pushVal: function() {
          var self = this,
              val = $.map(self.items(), function(item) {
                return self.options.itemValue(item).toString();
              });

          self.$element.val(val, true).trigger('change');
        },

        /**
         * Initializes the tags input behaviour on the element
         */
        build: function(options) {
          var self = this;

          self.options = $.extend({}, defaultOptions, options);
          // When itemValue is set, freeInput should always be false
          if (self.objectItems)
            self.options.freeInput = false;

          makeOptionItemFunction(self.options, 'itemValue');
          makeOptionItemFunction(self.options, 'itemText');
          makeOptionFunction(self.options, 'tagClass');

          // Typeahead Bootstrap version 2.3.2
          if (self.options.typeahead) {
            var typeahead = self.options.typeahead || {};

            makeOptionFunction(typeahead, 'source');

            self.$input.typeahead($.extend({}, typeahead, {
              source: function (query, process) {
                function processItems(items) {
                  var texts = [];

                  for (var i = 0; i < items.length; i++) {
                    var text = self.options.itemText(items[i]);
                    map[text] = items[i];
                    texts.push(text);
                  }
                  process(texts);
                }

                this.map = {};
                var map = this.map,
                    data = typeahead.source(query);

                if ($.isFunction(data.success)) {
                  // support for Angular callbacks
                  data.success(processItems);
                } else if ($.isFunction(data.then)) {
                  // support for Angular promises
                  data.then(processItems);
                } else {
                  // support for functions and jquery promises
                  $.when(data)
                   .then(processItems);
                }
              },
              updater: function (text) {
                self.add(this.map[text]);
                return this.map[text];
              },
              matcher: function (text) {
                return (text.toLowerCase().indexOf(this.query.trim().toLowerCase()) !== -1);
              },
              sorter: function (texts) {
                return texts.sort();
              },
              highlighter: function (text) {
                var regex = new RegExp( '(' + this.query + ')', 'gi' );
                return text.replace( regex, "<strong>$1</strong>" );
              }
            }));
          }

          // typeahead.js
          if (self.options.typeaheadjs) {
              var typeaheadConfig = null;
              var typeaheadDatasets = {};

              // Determine if main configurations were passed or simply a dataset
              var typeaheadjs = self.options.typeaheadjs;
              if ($.isArray(typeaheadjs)) {
                typeaheadConfig = typeaheadjs[0];
                typeaheadDatasets = typeaheadjs[1];
              } else {
                typeaheadDatasets = typeaheadjs;
              }

              self.$input.typeahead(typeaheadConfig, typeaheadDatasets).on('typeahead:selected', $.proxy(function (obj, datum) {
                if (typeaheadDatasets.valueKey)
                  self.add(datum[typeaheadDatasets.valueKey]);
                else
                  self.add(datum);
                self.$input.typeahead('val', '');
              }, self));
          }

          self.$container.on('click', $.proxy(function(event) {
            if (! self.$element.attr('disabled')) {
              self.$input.removeAttr('disabled');
            }
            self.$input.focus();
          }, self));

            if (self.options.addOnBlur && self.options.freeInput) {
              self.$input.on('focusout', $.proxy(function(event) {
                  // HACK: only process on focusout when no typeahead opened, to
                  //       avoid adding the typeahead text as tag
                  if ($('.typeahead, .twitter-typeahead', self.$container).length === 0) {
                    self.add(self.$input.val());
                    self.$input.val('');
                  }
              }, self));
            }


          self.$container.on('keydown', 'input', $.proxy(function(event) {
            var $input = $(event.target),
                $inputWrapper = self.findInputWrapper();

            if (self.$element.attr('disabled')) {
              self.$input.attr('disabled', 'disabled');
              return;
            }

            switch (event.which) {
              // BACKSPACE
              case 8:
                if (doGetCaretPosition($input[0]) === 0) {
                  var prev = $inputWrapper.prev();
                  if (prev.length) {
                    self.remove(prev.data('item'));
                  }
                }
                break;

              // DELETE
              case 46:
                if (doGetCaretPosition($input[0]) === 0) {
                  var next = $inputWrapper.next();
                  if (next.length) {
                    self.remove(next.data('item'));
                  }
                }
                break;

              // LEFT ARROW
              case 37:
                // Try to move the input before the previous tag
                var $prevTag = $inputWrapper.prev();
                if ($input.val().length === 0 && $prevTag[0]) {
                  $prevTag.before($inputWrapper);
                  $input.focus();
                }
                break;
              // RIGHT ARROW
              case 39:
                // Try to move the input after the next tag
                var $nextTag = $inputWrapper.next();
                if ($input.val().length === 0 && $nextTag[0]) {
                  $nextTag.after($inputWrapper);
                  $input.focus();
                }
                break;
             default:
                 // ignore
             }

            // Reset internal input's size
            var textLength = $input.val().length,
                wordSpace = Math.ceil(textLength / 5),
                size = textLength + wordSpace + 1;
            $input.attr('size', Math.max(this.inputSize, $input.val().length));
          }, self));

          self.$container.on('keypress', 'input', $.proxy(function(event) {
             var $input = $(event.target);

             if (self.$element.attr('disabled')) {
                self.$input.attr('disabled', 'disabled');
                return;
             }

             var text = $input.val(),
             maxLengthReached = self.options.maxChars && text.length >= self.options.maxChars;
             if (self.options.freeInput && (keyCombinationInList(event, self.options.confirmKeys) || maxLengthReached)) {
                // Only attempt to add a tag if there is data in the field
                if (text.length !== 0) {
                   self.add(maxLengthReached ? text.substr(0, self.options.maxChars) : text);
                   $input.val('');
                }

                // If the field is empty, let the event triggered fire as usual
                if (self.options.cancelConfirmKeysOnEmpty === false) {
                   event.preventDefault();
                }
             }

             // Reset internal input's size
             var textLength = $input.val().length,
                wordSpace = Math.ceil(textLength / 5),
                size = textLength + wordSpace + 1;
             $input.attr('size', Math.max(this.inputSize, $input.val().length));
          }, self));

          // Remove icon clicked
          self.$container.on('click', '[data-role=remove]', $.proxy(function(event) {
            if (self.$element.attr('disabled')) {
              return;
            }
            self.remove($(event.target).closest('.tag').data('item'));
          }, self));

          // Only add existing value as tags when using strings as tags
          if (self.options.itemValue === defaultOptions.itemValue) {
            if (self.$element[0].tagName === 'INPUT') {
                self.add(self.$element.val());
            } else {
              $('option', self.$element).each(function() {
                self.add($(this).attr('value'), true);
              });
            }
          }
        },

        /**
         * Removes all tagsinput behaviour and unregsiter all event handlers
         */
        destroy: function() {
          var self = this;

          // Unbind events
          self.$container.off('keypress', 'input');
          self.$container.off('click', '[role=remove]');

          self.$container.remove();
          self.$element.removeData('tagsinput');
          self.$element.show();
        },

        /**
         * Sets focus on the tagsinput
         */
        focus: function() {
          this.$input.focus();
        },

        /**
         * Returns the internal input element
         */
        input: function() {
          return this.$input;
        },

        /**
         * Returns the element which is wrapped around the internal input. This
         * is normally the $container, but typeahead.js moves the $input element.
         */
        findInputWrapper: function() {
          var elt = this.$input[0],
              container = this.$container[0];
          while(elt && elt.parentNode !== container)
            elt = elt.parentNode;

          return $(elt);
        }
      };

      /**
       * Register JQuery plugin
       */
      $.fn.tagsinput = function(arg1, arg2, arg3) {
        var results = [];

        this.each(function() {
          var tagsinput = $(this).data('tagsinput');
          // Initialize a new tags input
          if (!tagsinput) {
              tagsinput = new TagsInput(this, arg1);
              $(this).data('tagsinput', tagsinput);
              results.push(tagsinput);

              if (this.tagName === 'SELECT') {
                  $('option', $(this)).attr('selected', 'selected');
              }

              // Init tags from $(this).val()
              $(this).val($(this).val());
          } else if (!arg1 && !arg2) {
              // tagsinput already exists
              // no function, trying to init
              results.push(tagsinput);
          } else if(tagsinput[arg1] !== undefined) {
              // Invoke function on existing tags input
                if(tagsinput[arg1].length === 3 && arg3 !== undefined){
                   var retVal = tagsinput[arg1](arg2, null, arg3);
                }else{
                   var retVal = tagsinput[arg1](arg2);
                }
              if (retVal !== undefined)
                  results.push(retVal);
          }
        });

        if ( typeof arg1 == 'string') {
          // Return the results from the invoked function calls
          return results.length > 1 ? results : results[0];
        } else {
          return results;
        }
      };

      $.fn.tagsinput.Constructor = TagsInput;

      /**
       * Most options support both a string or number as well as a function as
       * option value. This function makes sure that the option with the given
       * key in the given options is wrapped in a function
       */
      function makeOptionItemFunction(options, key) {
        if (typeof options[key] !== 'function') {
          var propertyName = options[key];
          options[key] = function(item) { return item[propertyName]; };
        }
      }
      function makeOptionFunction(options, key) {
        if (typeof options[key] !== 'function') {
          var value = options[key];
          options[key] = function() { return value; };
        }
      }
      /**
       * HtmlEncodes the given value
       */
      var htmlEncodeContainer = $('<div />');
      function htmlEncode(value) {
        if (value) {
          return htmlEncodeContainer.text(value).html();
        } else {
          return '';
        }
      }

      /**
       * Returns the position of the caret in the given input field
       * http://flightschool.acylt.com/devnotes/caret-position-woes/
       */
      function doGetCaretPosition(oField) {
        var iCaretPos = 0;
        if (document.selection) {
          oField.focus ();
          var oSel = document.selection.createRange();
          oSel.moveStart ('character', -oField.value.length);
          iCaretPos = oSel.text.length;
        } else if (oField.selectionStart || oField.selectionStart == '0') {
          iCaretPos = oField.selectionStart;
        }
        return (iCaretPos);
      }

      /**
        * Returns boolean indicates whether user has pressed an expected key combination.
        * @param object keyPressEvent: JavaScript event object, refer
        *     http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
        * @param object lookupList: expected key combinations, as in:
        *     [13, {which: 188, shiftKey: true}]
        */
      function keyCombinationInList(keyPressEvent, lookupList) {
          var found = false;
          $.each(lookupList, function (index, keyCombination) {
              if (typeof (keyCombination) === 'number' && keyPressEvent.which === keyCombination) {
                  found = true;
                  return false;
              }

              if (keyPressEvent.which === keyCombination.which) {
                  var alt = !keyCombination.hasOwnProperty('altKey') || keyPressEvent.altKey === keyCombination.altKey,
                      shift = !keyCombination.hasOwnProperty('shiftKey') || keyPressEvent.shiftKey === keyCombination.shiftKey,
                      ctrl = !keyCombination.hasOwnProperty('ctrlKey') || keyPressEvent.ctrlKey === keyCombination.ctrlKey;
                  if (alt && shift && ctrl) {
                      found = true;
                      return false;
                  }
              }
          });

          return found;
      }

      /**
       * Initialize tagsinput behaviour on inputs and selects which have
       * data-role=tagsinput
       */
      $(function() {
        $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
      });
    })(window.jQuery);

      function copy(id, previousId) {
        tagsElementSrc = document.getElementsByName('tags-'+previousId)[0];
        tagsElementTrg = document.getElementsByName('tags-'+id)[0];
        var tagsToAdd = tagsElementSrc.value.split(","); 
        tagsToAdd.forEach(function(tag) {
            $('[name=tags-'+id+']').tagsinput('add', tag);
        });
      }
    var allIDs = [];
      function copy_all(id) {
        allIDs.forEach(function(trgId) {
            tagsElementSrc = document.getElementsByName('tags-'+id)[0];
            tagsElementTrg = document.getElementsByName('tags-'+trgId)[0];
            var tagsToAdd = tagsElementSrc.value.split(","); 
                
            tagsToAdd.forEach(function(tag) {
                $('[name=tags-'+trgId+']').tagsinput('add', tag);
            });
        })
      }
      function addTag(id, tag) {
        $('[name=tags-'+id+']').tagsinput('add', tag);
      }
    </script>
    <style>
    /* bootstrap-tagsinput.css file - add in local */

    .bootstrap-tagsinput {
      background-color: #fff;
      border: 1px solid #ccc;
      box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
      display: inline-block;
      padding: 4px 6px;
      color: #555;
      vertical-align: middle;
      border-radius: 4px;
      max-width: 100%;
      line-height: 22px;
      cursor: text;
    }
    .bootstrap-tagsinput input {
      border: none;
      box-shadow: none;
      outline: none;
      background-color: transparent;
      padding: 0 6px;
      margin: 0;
      width: 300px;
      max-width: inherit;
    }
    .bootstrap-tagsinput.form-control input::-moz-placeholder {
      color: #777;
      opacity: 1;
    }
    .bootstrap-tagsinput.form-control input:-ms-input-placeholder {
      color: #777;
    }
    .bootstrap-tagsinput.form-control input::-webkit-input-placeholder {
      color: #777;
    }
    .bootstrap-tagsinput input:focus {
      border: none;
      box-shadow: none;
    }
    .bootstrap-tagsinput .tag {
      margin-right: 2px;
      color: white;
    }
    .bootstrap-tagsinput .tag [data-role="remove"] {
      margin-left: 8px;
      cursor: pointer;
    }
    .bootstrap-tagsinput .tag [data-role="remove"]:after {
      content: "x";
      padding: 0px 2px;
    }
    .bootstrap-tagsinput .tag [data-role="remove"]:hover {
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    .bootstrap-tagsinput .tag [data-role="remove"]:hover:active {
      box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    }
    </style>
  </head>
  <body>
	<?php
        $queryCikIr = "SELECT Count(`Id`) as `count` FROM `ratings` WHERE `ratings`.`img` NOT IN (SELECT `img` FROM `tags`)";
        $dataCikIr = mysqli_query($connection, $queryCikIr);
		$reqCikIr = mysqli_fetch_array($dataCikIr);
	?>
	<form style="margin-top:-10px;" action="add-tag.php" method="post">
	
        <table border="1" style="margin:20px">
        <tr>
            <th>Album</th>
            <th>Image</th>
            <th style="width: 300px;">Tags</th>
            <th>Popular Tags</th>
        </tr>
        <?php
        $query = "SELECT `Id`, `album`, `img` FROM `ratings` WHERE `ratings`.`img` NOT IN (SELECT `img` FROM `tags`) ORDER BY `album` Limit 0, 6";
        $color = random_color();
        $previousAlbum = "";
        
        $data = mysqli_query($connection, $query);
		$previousId = 0;
        
        //Populārās birkas
        $popQuery = "SELECT tag ,COUNT(1) as Count FROM tags GROUP BY tag ORDER BY Count DESC LIMIT 0, 50";
        $popQueryRez = mysqli_query($connection, $popQuery);
        $popTags = array();
        while($popReq = mysqli_fetch_array($popQueryRez)){
            $popTags[] = trim($popReq['tag']);
        }
        natcasesort($popTags);
        natcasesort($recentlyAddedTags);
        $popTags = array_unique($popTags);
        
        while($req = mysqli_fetch_array($data)){
            $Id		= $req["Id"];
            $album	= $req["album"];
            $img	= $req["img"];
            
            if($previousAlbum != $album){
                $color = random_color();
                $previousAlbum = $album;
            }
            
            // //Vai šī albuma citām bildēm jau ir atrašanās vieta? Vai visām aptuveni viena? Varbūt var automātiski piedāvāt aizpildīt ar to pašu?
            // $albumQuery = "SELECT DISTINCT(CONCAT(`lat`, `lng`)) as `latlng`, `lat`, `lng` FROM `tags` WHERE `album` LIKE '$album' GROUP BY CONCAT(`lat`, `lng`) ";
            // $albumQueryRez = mysqli_query($connection, $albumQuery);
            // $resultCount = mysqli_num_rows($albumQueryRez);
            
            // if($resultCount == 1 ) {
                // $albumReq = mysqli_fetch_array($albumQueryRez);
                // $lat = $albumReq['lat'];
                // $lng = $albumReq['lng'];
            // }else{
                // $lat = "";
                // $lng = "";
            // }
            
            echo "<tr>";
            echo "<td style='background-color: #".$color."' >".$album."</td><td>";
            if(substr($img, 0, 4) == "http"){
                echo "<img style='max-width: 200px; max-height: 200px;' onload='(function(){ allIDs.push(".$Id."); }).call(this)' src='".$img."' />";
            }else{
                echo "<img style='max-width: 200px; max-height: 200px;' onload='(function(){var imgElement = this; var jsonURL=\"https://photoslibrary.googleapis.com/v1/mediaItems/".$img."?access_token=".$accessToken."\"; $.getJSON(jsonURL, function(data) { var imgURL = data.baseUrl+\"=w2000\"; imgElement.src=imgURL; allIDs.push(".$Id."); }); }).call(this)' src='includes/bigLoader.gif'/>";
            }
            echo "<input type='hidden' name='img-".$Id."' id='img-".$Id."' value='".$img."'>";
            echo "</td>";
            echo "<td style=\"width: 300px;\"><input type='text' value='' name='tags-".$Id."' data-role='tagsinput' placeholder='Add tags' /> ";
            echo "<input type='button' name='".$Id."' onclick='copy(".($Id.",".$previousId).");' value='Copy Previous'>";
            echo "<input type='button' name='".$Id."' onclick='copy_all(".$Id.");' value='Copy All'></td>";
            echo "<td style='padding:3px;'>";
            foreach($recentlyAddedTags as $rTag){
                echo "   <a style='border: 1px solid red; border-radius: 3px; padding: 2px; line-height: 2; margin: 2px;' href='#' onclick='addTag(".($Id.",\"".$rTag)."\");'>".$rTag."</a>   ";
            }
            foreach($popTags as $popTag){
                echo "   <a style='border: 1px solid black; border-radius: 3px; padding: 2px; line-height: 2; margin: 2px;' href='#' onclick='addTag(".($Id.",\"".$popTag)."\");'>".$popTag."</a>   ";
            }
            echo "</td>";
            echo "</tr>";
			
			$previousId = $Id;
        }
        
        function random_color_part() {
            return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
        }

        function random_color() {
            return random_color_part() . random_color_part() . random_color_part();
        }
        ?>
        </table>
		<input type="submit" name="submit" value="Submit">
        <?php
            echo "Atlicis: ".$reqCikIr['count'];
        ?>
	</form>
  </body>
</html>