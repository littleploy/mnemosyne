var numOfCard,counter=1,updateWindowHeight=function(){$("#card-container").height($(window).height()-300)},disableInActiveCard=function(){$(".right-card").find("*").prop("disabled",!0),$(".middle-card").find("*").prop("disabled",!1).find(".submit-button").prop("disabled",!0)},flipCard=function(){var e=$(this).parents(".card-wrapper");e.hasClass("flipped")?e.removeClass("flipped"):e.addClass("flipped")},checkAnswer=function(){var e=0;counter===numOfCard&&(e=1);var a=$(this).parent().find("input[type=text]");$.ajax({type:"POST",url:"/mnemosyne/Decks/validateCard",data:{id:a.attr("id"),value:a.attr("data-answer"),lastCard:e}}).done(function(e){var a=$.parseJSON(e);"redirect"===a.action&&(window.location.href=a.value),"correct"===a.action?($("#notification p").html('<i class="mdi-navigation-check"></i> Correct :)'),$("#notification").addClass("correct").removeClass("wrong").fadeIn(1e3).fadeOut(3e3)):($("#notification p").html('<i class="mdi-navigation-close"></i> Wrong :('),$("#notification").addClass("wrong").removeClass("correct").fadeIn(1e3).fadeOut(3e3))});var t=$(this).parents(".card");t.next().addClass("middle-card").removeClass("right-card"),t.addClass("move-off").fadeOut(500),disableInActiveCard(),counter++,$("#counter").text(counter+" of "+numOfCard)},init=function(){$(window).resize(updateWindowHeight),updateWindowHeight(),$(".answer-button").click(flipCard),disableInActiveCard(),$(".preloader-wrapper").fadeOut(1400),$(".top-bottom-padding-adjustment").fadeIn(2e3),numOfCard=$(".card").length,$("#counter").text(counter+" of "+numOfCard),$(".submit-button").click(checkAnswer),$(".collection-item").draggable({revert:"invalid",snap:".droppable",helper:"clone",snapMode:"corner",snapTolerance:"22"}),$(".input-field input[type=text]").droppable({accept:".collection-item",drop:function(e,a){$(this).val($(a.draggable).text()),$(this).attr("data-answer",$(a.draggable).html()),$(this).parent().siblings(".submit-button").prop("disabled",!1)}})};$(document).ready(init);