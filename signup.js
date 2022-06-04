// File: signup.js
// Name: Leo Qian
// Class: CS325, Jan 2022
// Final Project
// Due date: Last day of class

"use strict;"

$(document).ready(function(){
    // after user finish confirming the password, check if the password matches
    $("#password2").focusout(comparePasswd);

    // when submit the form, check if any of the field is empty
    $(".form").submit(function(){
        if($("#username").val()=="" || $("#email").val()=="" || $("#password1").val()=="" || $("#password2").val()==""){
            alert("one or more field on this page is empty!")
            return false;
        }
        // compare the two password again when submit
        return comparePasswd();
    })

})

function comparePasswd(){
    if ($("#password1").val()!=$("#password2").val()) {
        alert("Your password doesn't match!")
        return false;
    }
    else {
        return true;
    }
}

