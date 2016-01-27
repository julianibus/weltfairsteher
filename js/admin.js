// Generated by CoffeeScript 1.9.1
var entityMap, escapeHtml, sendForm;

entityMap = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  '\'': '&#39;',
  '/': '&#x2F;'
};

escapeHtml = function(string) {
  return String(string).replace(/[&<>"'\/]/g, function(s) {
    return entityMap[s];
  });
};

sendForm = function(form) {
  var dest;
  dest = $(form).attr("id");
  $.post("admin/" + dest + ".php", $("#" + dest).serialize()).done(function(errors) {
    var error, i, len, list, resultDiv;
    console.log(errors);
    errors = JSON.parse(errors);
    resultDiv = $("#" + dest + " > .result");
    if (!resultDiv.length) {
      resultDiv = $("<div class='result'></div>");
      resultDiv.appendTo("#" + dest);
    }
    resultDiv.empty();
    if (errors.length) {
      list = $("<ul></ul>");
      for (i = 0, len = errors.length; i < len; i++) {
        error = errors[i];
        list.append("<li>" + (escapeHtml(error)) + "</li>");
      }
      return resultDiv.append(list);
    } else {
      return resultDiv.append("<b>Erfolgreich!</b>");
    }
  });
  return false;
};
