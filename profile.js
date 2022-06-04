// File: profile.js
// Name: Leo Qian
// Class: CS325, Jan 2022
// Final Project
// Due date: Last day of class

"use strict;"
$(document).ready(function(){

    // when submit the form, check if any of the field is empty
    $(".form").submit(function(){
        if($("#username").val()=="" || $("#email").val()==""){
            alert("one or more field on this page is empty!")
            return false;
        }
    })

})