// File: dashboard.js
// Name: Leo Qian
// Class: CS325, Jan 2022
// Final Project
// Due date: Last day of class

$(document).ready(function(){
    $("form").submit(function(){
        let filename = $('input[type=file]').val().split('\\').pop();
        
        // make sure the user only uploads a csv file
        if (!filename.match(/.[cC]{1}[sS]{1}[vV]{1}$/)) {
            alert("please upload only a csv file");
            return false;
        }
    })


    // ajax request
    $.post('graph.php',createGraph);

    // process the ajax request when selection changes
    $("select").change(function(){
        $.post('graph.php',createGraph);
    });

})

function createGraph(data,status) {
    if(status == "success") {
        $(".year").text($("select").val());
        let currentYear = $("select").val();

        let arr = data.split("*");
        let totalRev = 0.0;
        // initiate a map recording vendor and revenue
        let vendorArr = new Map();

        // initiate a array recording the cashflow
        let costArr = new Array(12).fill(0);
        let payArr = new Array(12).fill(0);
        let cashflow = [costArr,payArr];
        for (let line of arr) {
            if (line != ""){
                let lineArr = line.split(",");
                let year = lineArr[0].split("/")[2];
                let month = parseInt(lineArr[0].split("/")[0]);
                let cost = parseFloat(lineArr[2]);
                let payout = parseFloat(lineArr[3]);
                let vendor = lineArr[1];

                // if this is the current 
                if (year == currentYear) {
                    totalRev += payout;

                    if (vendorArr.has(vendor)) {
                        vendorArr[vendor] += payout;
                    }
                    else {
                        vendorArr.set(vendor,payout);
                    }
                    // add cashflow to the corresponding slot
                    cashflow[0][month] += cost;
                    cashflow[1][month] += payout;
                }
                
            }
        }
        makePie(vendorArr);
        makeLine(cashflow);
        
        $("#rev").text(totalRev.toFixed(2));
        
    } else {
        alert("Error making Ajax request:\n\nServer status:\n" + status);
    }
}

function makePie(map) {

    // remove and readd the canvas element to avoid hover event
    if ($("#pie").length) {
        $("#pie").remove();
    }
    
    let pie = document.createElement("canvas");
    pie.setAttribute("id","pie");
    $("#vendor").append(pie);

    let vendor = Array.from( map.keys() );
    let revenue = Array.from( map.values() );

    // if number of vendor is larger than 5, shorten it to 5 and mark the others as others
    if (vendor.length > 5) {
        let newVendor = vendor.slice(0,5);
        newVendor.push("Others");
        vendor = newVendor;
        let newRevenue = revenue.slice(0,5);
        let otherRev = revenue.slice(-(revenue.length-5)).reduce((partialSum, a) => partialSum + a, 0);
        newRevenue.push(otherRev);
        revenue = newRevenue;
    }

    let xValues = vendor;
    let yValues = revenue;
    let barColors = [];

    for (let value of xValues) {
        barColors.push(randomColor());
    }

    new Chart("pie", {
    type: "pie",
    data: {
        labels: xValues,
        datasets: [{
        backgroundColor: barColors,
        data: yValues
        }]
    },
    });
}

function makeLine(cashflow){

    let date = Array.from(Array(12).keys());
    date.shift();

    let cost = cashflow[0];
    let payout = cashflow[1];

    new Chart("line", {
    type: "line",
    data: {
        labels: date,
        datasets: [{
        label: 'Cost',
        data: cost,
        borderColor: "red",
        fill: false
        },{
        label: 'Payout',
        data: payout,
        borderColor: "green",
        fill: false
        }]
    },
    options: {
        legend: {display: true}
    }
    });
}

function randomColor() {
    let x=Math.round(0xffffff * Math.random()).toString(16);
    let y=(6-x.length);
    let z= "000000";
    let z1 = z.substring(0,y);
    let color= "#" + z1 + x;

    return color;
}