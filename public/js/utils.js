/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

function showHint(search, suggestbox, ajaxurl, fields) {

//si aspetta che search sia un array di n stringhe
//e che in ajaxurl compaiano segnaposto ###0###,..., ###n-1####
    var notready = true;
    var values = new Array();
    var params = "fields=" + fields.join("|");
    var j = 0;
    if (!(document.getElementById(suggestbox))) {
        return;
    }
    for (var i = 0; i < search.length; i++) {
        if ((search[i].length >= 2)) {
            notready = false;
        }
        if (!(document.getElementById(search[i]))) {
            console.log(search[i] + " " + document.getElementById(search[i]));
            return;
        }
        else {
            values[i] = document.getElementById(search[i]).value;
        }
    }
    if (notready) {
        document.getElementById(suggestbox).innerHTML = "";
        return;
    }

    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            if (document.getElementById(suggestbox)) {

                document.getElementById(suggestbox).innerHTML = xmlhttp.responseText;
            }
        }

    }
    for (var i = 0; i < search.length; i++) {
        ajaxurl = ajaxurl.replace("###" + i + "###", values[i])
    }
    console.log(ajaxurl);
    xmlhttp.open("POST", ajaxurl, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}



function completeform(fields, values) {
    for (var i = 0; i < fields.length; i++) {
        if (document.getElementById(fields[i])) {
            document.getElementById(fields[i]).value = values[i];
        }
    }

}



function editfield(step, idfield, picklist, type, ajaxsearchurl, opsearchid, ajaxediturl, opid, index) {
    var container = document.getElementById(idfield);
    var check = document.getElementById('check');
    if (!check) {
        check = document.createElement("span");
        check.id = 'check';
        document.getElementsByTagName('body')[0].appendChild(check);
        check.innerHTML = "token";
    }
    else if (check.innerHTML == "token" && step == 0) {
        return;
    }
    if (!(container)) {
        console.log(idfield + " non trovato");
        return;
    }
    container.style.textDecoration = "line-through";
    if (step == 0) {
        var inner = container.innerHTML;
        if (picklist) {
            var xmlhttp;
            if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            else {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
                {
                    var showedit = function(idfield, ajaxediturl, opid, index, responseText) {

                        setTimeout(function() {

                            container = document.getElementById(idfield);
                            container.innerHTML = responseText;
                            var sel = container.getElementsByTagName("select");
                            if (sel.length > 0) {
                                sel[0].setAttribute("id", idfield + "_edit");
//                        console.log(sel[0].innerHTML);
                                var opts = sel[0].getElementsByTagName("option");
//                        console.log("\n"+opts.length);
                                for (var i = 0; i < opts.length; i++) {
//                            console.log("\n"+opts[i].text+"\n"+inner);
                                    if (inner.trim() == opts[i].text) {
                                        var adjust = function(value, id) {
                                            setTimeout(function() {
                                                document.getElementById(id).selectedIndex = value;
                                            }, 100);
                                        }
                                        adjust(i, idfield + "_edit");
                                        break;
                                    }
                                }
                            }
                            container.innerHTML += "<input type=\"button\" value=\"OK\" onclick=\"editfield(1, '" + idfield + "', false, 'type', '', '', '" + ajaxediturl + "', '" + opid + "','" + index + "')\">";
                        }, 500);
                    }
                    showedit(idfield, ajaxediturl, opid, index, xmlhttp.responseText);
                }

            }

            ajaxsearchurl = ajaxsearchurl.replace("###0###", opsearchid);
//            console.log(ajaxsearchurl);
            xmlhttp.open("GET", ajaxsearchurl, true);
            xmlhttp.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
            xmlhttp.send();
        }
        else {
            var showedit = function(idfield, type, inner) {
                setTimeout(function() {
                    switch (type) {
                        case 'number':
                            container.innerHTML = "<input type=\"number\" id=\"" + idfield + "_edit\" value=\"" + inner + "\">";
                            break;
                        case 'date':
                            container.innerHTML = "<input type=\"date\" id=\"" + idfield + "_edit\" value=\"" + inner + "\">";
                            break;
                        default:
                            container.innerHTML = "<input type=\"text\" id=\"" + idfield + "_edit\" value=\"" + inner + "\">";
                            break;

                    }
                    container.innerHTML += "<input type=\"button\" value=\"OK\" onclick=\"editfield(1, '" + idfield + "', false, 'type', '', '', '" + ajaxediturl + "', '" + opid + "','" + index + "')\">";
                },500);
            
            }
            showedit(idfield, type, inner) ;

        }
//        console.log("fine");
        return;
    }
    else if (step == 1) {
//        console.log("cerco " + idfield + "_edit");
        var input = document.getElementById(idfield + "_edit");
        if (!(input))
            return;
        var datum = "";
        if (input.tagName == "INPUT") {
            var datum = input.value;
        }
        else if (input.tagName == "SELECT") {
            var datum = input.options[input.selectedIndex].value;
        }
//        console.log(input.tagName);
//        alert("datum = " + datum);
        var xmlhttp;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
            {
                if (xmlhttp.responseText == 'ok') {
                    if (input.tagName == "SELECT") {
                        datum = input.options[input.selectedIndex].text;
                    }
                    container.style.textDecoration = "none";
                    container.innerHTML = datum;
                    if (document.getElementById('check'))
                        ;
                    document.getElementById('check').parentNode.removeChild(document.getElementById('check'));
                }
                else {
                    container.innerHTML = "errore!";
                }

            }

        }

        ajaxediturl = ajaxediturl.replace("###0###", opid);
        ajaxediturl = ajaxediturl.replace("###1###", index);
        ajaxediturl = ajaxediturl.replace("###2###", datum);
//        console.log(ajaxediturl);
        xmlhttp.open("GET", ajaxediturl, true);
        xmlhttp.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
        xmlhttp.send();
    }

}


function editfieldwithrestriction(step, idfield, picklist, type, ajaxsearchurl, opsearchid, ajaxediturl, opid, index) {
    var container = document.getElementById(idfield);
    var check = document.getElementById('check');
    if (!check) {
        check = document.createElement("span");
        check.id = 'check';
        document.getElementsByTagName('body')[0].appendChild(check);
        check.innerHTML = "token";
    }
    else if (check.innerHTML == "token" && step == 0) {
        return;
    }
    if (!(container)) {
        console.log(idfield + " non trovato");
        return;
    }
    container.style.textDecoration = "line-through";
    if (step == 0) {
        var inner = container.innerHTML;
        if (picklist) {
            var xmlhttp;
            if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            else {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
                {
                    var showedit = function(idfield, ajaxediturl, opid, index, responseText) {

                        setTimeout(function() {

                            container = document.getElementById(idfield);
                            container.innerHTML = responseText;
                            var sel = container.getElementsByTagName("select");
                            if (sel.length > 0) {
                                sel[0].setAttribute("id", idfield + "_edit");
//                        console.log(sel[0].innerHTML);
                                var opts = sel[0].getElementsByTagName("option");
//                        console.log("\n"+opts.length);
                                for (var i = 0; i < opts.length; i++) {
//                            console.log("\n"+opts[i].text+"\n"+inner);
                                    if (inner.trim() == opts[i].text) {
                                        value = opts[i].value;
                                        var adjust = function(value, index, id) {
                                            setTimeout(function() {
                                                var sel = document.getElementById(id + "_edit");
                                                sel.selectedIndex = index;
                                                var opts = sel.getElementsByTagName("option");
                                                for (var i=0; i<opts.length;i++){
                                                    if (value == opts[i].value)
                                                        continue;
                                                    var bo = eval(id+"_check("+value+","+opts[i].value+")");
//                                                    console.log(value+" "+opts[i].value+" "+bo);
                                                    if(!bo){
                                                        opts[i].parentNode.removeChild(opts[i]);
                                                    }
                                                }
                                            }, 100);
                                        }
                                        adjust(value, i, idfield);
                                        break;
                                    }
                                }
                            }
                            container.innerHTML += "<input type=\"button\" value=\"OK\" onclick=\"editfieldwithrestriction(1, '" + idfield + "', false, 'type', '', '', '" + ajaxediturl + "', '" + opid + "','" + index + "','')\">";
                        }, 500);
                    }
                    showedit(idfield, ajaxediturl, opid, index, xmlhttp.responseText);
                }

            }

            ajaxsearchurl = ajaxsearchurl.replace("###0###", opsearchid);
//            console.log(ajaxsearchurl);
            xmlhttp.open("GET", ajaxsearchurl, true);
            xmlhttp.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
            xmlhttp.send();
        }
        else {
            var showedit = function(idfield, type, inner) {
                setTimeout(function() {
                    switch (type) {
                        case 'number':
                            container.innerHTML = "<input type=\"number\" id=\"" + idfield + "_edit\" value=\"" + inner + "\">";
                            break;
                        case 'date':
                            container.innerHTML = "<input type=\"date\" id=\"" + idfield + "_edit\" value=\"" + inner + "\">";
                            break;
                        default:
                            container.innerHTML = "<input type=\"text\" id=\"" + idfield + "_edit\" value=\"" + inner + "\">";
                            break;

                    }
                    container.innerHTML += "<input type=\"button\" value=\"OK\" onclick=\"editfieldwithrestriction(1, '" + idfield + "', false, 'type', '', '', '" + ajaxediturl + "', '" + opid + "','" + index + "','')\">";
                },500);
            
            }
            showedit(idfield, type, inner) ;

        }
//        console.log("fine");
        return;
    }
    else if (step == 1) {
//        console.log("cerco " + idfield + "_edit");
        var input = document.getElementById(idfield + "_edit");
        if (!(input))
            return;
        var datum = "";
        if (input.tagName == "INPUT") {
            var datum = input.value;
        }
        else if (input.tagName == "SELECT") {
            var datum = input.options[input.selectedIndex].value;
        }
//        console.log(input.tagName);
//        alert("datum = " + datum);
        var xmlhttp;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
            {
                if (xmlhttp.responseText == 'ok') {
                    if (input.tagName == "SELECT") {
                        datum = input.options[input.selectedIndex].text;
                    }
                    container.style.textDecoration = "none";
                    container.innerHTML = datum;
                    if (document.getElementById('check'))
                        ;
                    document.getElementById('check').parentNode.removeChild(document.getElementById('check'));
                }
                else {
                    container.innerHTML = "errore!";
                }

            }

        }

        ajaxediturl = ajaxediturl.replace("###0###", opid);
        ajaxediturl = ajaxediturl.replace("###1###", index);
        ajaxediturl = ajaxediturl.replace("###2###", datum);
//        console.log(ajaxediturl);
        xmlhttp.open("GET", ajaxediturl, true);
        xmlhttp.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
        xmlhttp.send();
    }

}


function prforrecop(url){
    console.log("ciao");
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            var rel = function (){
                setTimeout(function(){
                    location.reload();
                },100);
            };
            if (xmlhttp.responseText == 'ok') {
                console.log("cancello");
                rel();
            }
        }
        

    }
    
    xmlhttp.open("GET", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
}