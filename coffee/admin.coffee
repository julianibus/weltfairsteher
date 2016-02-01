
window.acceptSelfmade = ->
  sendForm "#acceptSelfmade", api: "addChallenge", cb: (errors) ->
    # there is a race condition between adding and deleting here, but we don't care
    return if errors.length
    callApi "deleteChallenge", suggested: "1", challenge: $("#selfmadeSelect").val()

selectSelfmade = ->
  id = $("#selfmadeSelect").val()
  console.log "selected:", id
  for prop in ["title", "points", "class", "description"]
    $("#acceptSelfmade >> [name='#{prop}']").val(suggestedChallenges[id][prop])
  $("#class-name").text suggestedChallenges[id].name

$('document').ready ->
  first = null
  for id of suggestedChallenges
    first = id
    break
  return unless first?
  $("#selfmadeSelect").val first
  $("#selfmadeSelect").change selectSelfmade
  selectSelfmade()
