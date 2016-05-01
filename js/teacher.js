// Generated by CoffeeScript 1.9.1
(function() {
  var challengeChange, classChange;

  classChange = function() {
    var challenge, i, len, ref, results;
    $("#challenges").empty();
    ref = challengesForClass[$("#class").val()];
    results = [];
    for (i = 0, len = ref.length; i < len; i++) {
      challenge = ref[i];
      results.push($("#challenges").append($("<option></option>").attr("value", challenge.id).text(challenge.name)));
    }
    return results;
  };

  challengeChange = function() {
    var ref;
    return $("#extra").toggle(((ref = _.find(challenges, {
      id: +$("#challenges").val()
    })) != null ? ref.extrapoints : void 0) != null);
  };

  $('document').ready(function() {
    classChange();
    challengeChange();
    $("#class").change(classChange);
    return $("#challenges").change(challengeChange);
  });

}).call(this);
