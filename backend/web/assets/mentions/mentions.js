"use strict"

function Mentions(id, options) {

  var self = this;

  var element = document.getElementById(id);
  element.setAttribute('contenteditable', 'true');

  var container = document.getElementById(options.container);

  var currentDelimiter = null;
  var currentNode = null;
  var currentOffset = 0;

  this.providers = [];
  this.delimiters = [];

  var openList = false;
  var navigation = false;

  options.providers.forEach(function(item) {
    self.delimiters.push(item.delimiter);
    self.providers[item.delimiter] = item.data;
  });

  this.selectItem = function(item) {

    var tag = document.createElement('span');
    tag.setAttribute('class', 'mention-tag');
    tag.setAttribute('contenteditable', 'false');
    tag.setAttribute('data-delimiter', currentDelimiter);
    if (item.id) tag.setAttribute('data-mention', item.id);
    var text = document.createTextNode(currentDelimiter + item.name);
    tag.appendChild(text);

    var previousText = currentNode.splitText(currentOffset);

    var textTokens = previousText.previousSibling.data.split(currentDelimiter);
    textTokens.splice(-1, 1);

    var stringTokens = textTokens.join(currentDelimiter);

    previousText.previousSibling.data = stringTokens;
    previousText.insertData(0, "\u00A0");

    currentNode.parentNode.insertBefore(tag, previousText);

    var range = document.createRange();
    var selection = window.getSelection();

    range.setStart(previousText, 1);
    range.collapse(true);
    selection.removeAllRanges();
    selection.addRange(range);

    this.renderItems([]);
  };

  this.renderItems = function(items) {

    container.scrollTop = 0;

    container.style.display = 'none';
    container.innerHTML = '';

    var found = false;

    items.forEach(function(item) {

      var child = document.createElement('li');
      var text = document.createTextNode(item.name);
      child.appendChild(text);
      child.setAttribute('data-itemId', item.id);

      child.addEventListener('click', function(event) {
        self.selectItem(item);
      });

      child.addEventListener('mouseover', function(event) {
        Object.values(container.children).forEach(function(li) {
          li.removeAttribute('class');
        });
        this.setAttribute('class', 'mention-focus');
      });

      container.appendChild(child);
      found = true;
    });

    openList = false;
    if (found) {
      openList = true;
      container.style.display = 'block';
    }
  };

  element.addEventListener('keydown', function(evt) {
    var current = null;
    var position = null;

    var keyCode = evt.keyCode;

    var codesNavigation = [38,40,13,27];

    if (($.inArray(keyCode, codesNavigation) !== -1) && openList) {

      if (keyCode === 27) {
        self.renderItems([]);
      } else {
        Object.values(container.children).forEach(function(li, i) {

          if (!current) {
            var regex = new RegExp('mention-focus', 'g');
            if (regex.test(li.getAttribute("class"))) {
              current = li;
              position = i;

              var movement = '';
              if (keyCode === 40 && i < container.children.length - 1 ) {
                position++;
                movement = 'down';
              } else if (keyCode === 38 && i > 0 ) {
                position--;
                movement = 'up';
              }
              li.setAttribute('class', '');
              Object.values(container.children)[position].setAttribute('class', 'mention-focus');

              // Auto Scroll

              var liHeight = Object.values(container.children)[position].clientHeight;
              var liOffsetTop = Object.values(container.children)[position].offsetTop;
              var containerHeight = container.clientHeight;
              var scrollTop = container.scrollTop;
              var liDownAdjust = (liHeight + liOffsetTop - scrollTop) - containerHeight;
              liDownAdjust = liDownAdjust >= 0 ? liDownAdjust : 0;

              if (movement === 'down') {
                if (liDownAdjust > 0) {
                  container.scrollTop = scrollTop + liDownAdjust;
                }
              } else if (movement === 'up') {
                if (liOffsetTop < scrollTop) {
                  container.scrollTop = liOffsetTop;
                }
              }
            }
          }
        });

        if (!current && (keyCode === 38 || keyCode === 40)) {
          Object.values(container.children)[0].setAttribute('class', 'mention-focus');
        }

        if (keyCode === 13 && !current) {
          navigation = false;
        } else {
          evt.preventDefault();
          navigation = true;

          if (keyCode === 13 && current) {

            self.providers[currentDelimiter].forEach(function(item) {
              if (item.id == current.getAttribute('data-itemId')) {
                self.selectItem(item);
              }
            });
          }
        }
      }
    } else {
      navigation = false;
    }
  });

  element.addEventListener('keyup', function(event) {

    if(!navigation) {

      var selection = window.getSelection();
      var anchorOffset = selection.anchorOffset;

      currentNode = selection.anchorNode;
      currentOffset = anchorOffset;

      var pr = selection.focusNode.data;
      var sub = anchorOffset && pr ? pr.substring(0, anchorOffset) : '';
      var sp = sub.split(' ').reverse()[0];
      var nextSp = sp.replace(/\s/g, '');
      var delimiter = nextSp.substring(0, 1);
      var pattern = nextSp.substring(1); // empty search?
      var items = [];
      var assert = self.delimiters.indexOf(delimiter);

      if(assert >= 0) {

        var regex = new RegExp(pattern, 'i');

        var data = self.providers[delimiter];

        currentDelimiter = delimiter;

        data.forEach(function(item) {
          if(regex.test(item.name)) {
            items.push(item);
          }
        });

        self.renderItems(items);
      } else {
        self.renderItems([]);
      }
    }
  });
}
