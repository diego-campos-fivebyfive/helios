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

    var stringTokens = textTokens.join('');

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

    container.style.display = 'none';
    container.innerHTML = '';

    var found = false;

    items.forEach(function(item) {

      var child = document.createElement('li');
      var text = document.createTextNode(item.name);
      child.appendChild(text);

      child.addEventListener('click', function(event) {
        self.selectItem(item);
      });

      container.appendChild(child);

      found = true;
    });

    if (found) container.style.display = 'block';
  };


  element.addEventListener('keyup', function(event) {

    var charCode = event.charCode;
    var selection = window.getSelection();
    var focusNode = selection.focusNode;
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
  });
}
