/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */
// return a random alphabetic string of length ll;
function random_char_string(ll) {
    if (Number(ll) <= 0)
        return "";
    var res = "";
    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    for (var i = 0; i < ll; i++) {
//        console.log(chars[Math.round((chars.length - 1) * Math.random())]);
        res += chars.charAt(Math.round((chars.length - 1) * Math.random()));
    }
    return res;
}

function random_char_string_for_id(ll, prefix, postfix) {
    if (Number(ll) <= 0)
        return "";
    if (typeof (prefix) === "undefined") {
        prefix = "";
    }
    if (typeof (postfix) === "undefined") {
        postfix = "";
    }


    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    var flag = true; //
    while (flag) {
        var res = "";
        for (var i = 0; i < ll; i++) {
//            console.log(chars[Math.round((chars.length - 1) * Math.random())]);
            res += chars.charAt(Math.round((chars.length - 1) * Math.random()));
        }
        flag = document.getElementById(prefix + res + postfix);
    }
    return prefix + res + postfix;
}


function caldate2string(date, flag) {
    //date must be of type date
    //flag must be of type (1/2), 1=yyyy-mm-dd, 2=dd-mm-yyyy
    flag = Number(flag);
    if (date instanceof Date && !isNaN(date.valueOf()) && !isNaN(flag) && (flag >= 1) && (flag <= 2)) {
        var dd = date.getDate();
        if (dd < 10)
            dd = "0" + dd;
        var mm = 1 + Number(date.getMonth());
        if (mm < 10)
            mm = "0" + mm;
        var yy = date.getFullYear();
        if (yy < 10)
            yy = "000" + yy;
        else if (yy < 100)
            yy = "00" + yy;
        else if (yy < 1000)
            yy = "0" + yy;
        if (flag == 1)
            return yy + "-" + mm + "-" + dd;
        else
            return dd + "-" + mm + "-" + yy;
    }
    else
    {
        //TODO :: should throw an exception!
        return date;
    }
}

function string2caldate(string, flag) {
    //string must be dd-mm-yyyy or yyyy-mm-dd
    //flag must be of type (0/1/2), 0=auto, 1=yyyy-mm-dd, 2=dd-mm-yyyy
    flag = Number(flag);
    if (string.length == 10) {
        switch (flag) {
            case 0:
            case '0':
                //try to understand format
                var array = string.split("-");
                if (array.length != 3) {
                    return new Array(-1, string); //TODO:: throw exception!
                }
                else {
                    if ((array[0].length == 2) && (array[1].length == 2) && (array[2].length == 4)) {
                        return new Array(2, new Date(array[2] + "-" + array[1] + "-" + array[0]));
                    }
                    else if ((array[0].length == 4) && (array[1].length == 2) && (array[2].length == 2)) {
                        return new Array(1, new Date(string));
                    }
                }
                break;
            case 1:
            case '1':

                return new Array(1, new Date(string));
                break;
            case 2:
            case '2':
                var array = string.split("-");
                return new Array(2, Date(array[2] + "-" + array[1] + "-" + array[0]));
                break;

        }
    }
}

function strcomp(s1, s2) {
    var minlen = Math.min(s1.length, s2.length);
    var div = false;
    for (var i = 0; i < minlen; i++) {
        if (s1.charCodeAt(i) === s2.charCodeAt(i)) {
            continue;
        }
        else if (s1.charCodeAt(i) > s2.charCodeAt(i)) {
            return 1;
        }
        else if (s1.charCodeAt(i) < s2.charCodeAt(i)) {
            return -1;
        }
    }
    if (!(div)) {
        if (s1.length > s2.length) {
            return 1;
        }
        else if (s1.length == s2.length)
            return 0;
    }
    return -1;
}

function strdatecomp(s1, s2) {
    // prende due stringhe del tipo cccc-cc-cc oppure cc-cc-cccc
    //capisce il formato e le confronta come date

    //provo ita:
    if (s1 == "")
        s1 = "00-00-0000";
    if (s2 == "")
        s2 = "00-00-0000";
    var match1 = s1.match(/(\d{2})-(\d{2})-(\d{4})/);
    if (match1 != null && typeof (match1.length) != "undefined" && match1.length == 4) {
        var giorno1 = match1[1];
        var mese1 = match1[2];
        var anno1 = match1[3];
    }
    else {
        var match1 = s1.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match1 != null && typeof (match1.length) != "undefined" && match1.length == 4) {
            var anno1 = match1[1];
            var mese1 = match1[2];
            var giorno1 = match1[3];
        }
        else {
            return 0;
        }
    }

    var match2 = s2.match(/(\d{2})-(\d{2})-(\d{4})/);
    if (match2 != null && typeof (match2.length) != "undefined" && match2.length == 4) {
        var giorno2 = match2[1];
        var mese2 = match2[2];
        var anno2 = match2[3];
    }
    else {
        var match2 = s2.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match2 != null && typeof (match2.length) != "undefined" && match2.length == 4) {
            var anno2 = match2[1];
            var mese2 = match2[2];
            var giorno2 = match2[3];
        }
        else {
            return 0;
        }
    }

    if (anno1 == anno2) {
        if (mese1 == mese2) {
            if (giorno1 == giorno2) {
                return 0;
            }
            else if (giorno1 < giorno2) {
                return -1
            }
            else {
                return 1;
            }
        }
        else if (mese1 < mese2) {
            return -1
        }
        else {
            return 1;
        }
    }
    else if (anno1 < anno2) {
        return -1;
    }
    else {
        return 1;
    }
}


function wolfot_changeOrder(a) {
    if (!a)
        return;
    var href = a.href ;
      href = href.replace("new Array(true)", "pippo");
    href = href.replace("new Array(false)", "new Array(true)");
    href = href.replace("pippo", "new Array(false)");
    a.href = href;
    return false;
//    console.log($(a).parent().html());
//    var href = $(a).attr("href");
//    href = href.replace("new Array(true)", "new Array(false)");
//    href = href.replace("new Array(false)", "new Array(true)");
//    $(a).attr("href", href);
//    console.log(href);
//    console.log($(a).attr("href"));
}


function random_color(){
    
    var colors = ["#A0CE00","#00FFFF","#F0FFFF","#FFE4C4","#FFEBCD","#8A2BE2","#DEB887","#7FFF00","#FF7F50","#FFF8DC","#00FFFF","#483D8B","#B8860B","#FF1493","##696969","#822222","#228B22","#DCDCDC","#FFD700","#808080","#ADFF2F","#FF69B4","#4B0082","#F0E68C","#FFF0F5","#ADD8E6","#E0FFFF","#90EE90","#FFB6C1","#20B2AA","#778899","#FFFFE0","#32CD32","#FF00FF","#66CDAA","#BA55D3","#3CB371","#00FA9A","#C71585","#F5FFFA","#FFDEAD","#FDF5E6","#6B8E23","#FF4500","#EEE8AA","#AFEEEE","#FFEFD5","#CD853F","#DDA0DD","#800080","#BC8F8F","#8B4513","#F4A460","#FFF5EE","#C0C0C0","#6A5ACD","#FFFAFA","#468284","#008080","#FF6347","#EE82EE","#FFFF00","#FAEBD7","#7FFFD4","#F5F5DC","#000000","#0000FF","#A52A2A","#5F9EA0","#D2691E","#6495ED","#DC143C","#00008B","##008B8B","#A9A9A9","#00BFFF","#1E90FF","#FFFAF0","#FF00FF","#F8F8FF","#DAA520","#008800","#F0FFF0","#CD5C5C","#FFFFF0","#E6E6FA","#FFFACD","#F08080","#FAFAD2","#D3D3D3","#FFA07A","#87CEFA","#B0C4DE","#00FF00","#FAF0E6","#800000","#0000CD","#9370DB","#7B68EE","#48D1CC","#191970","#FFE4E1","#000080","#808000","#FFA500","#DA70D6","#98FB98","#DB7093","#FFDAB9","#FFC0CB","#B0E0E6","#FF0000","#4169E1","#FA8072","#2E8B57","#A0522D","#87CEEB","#708090","#00FF7F","#D2B48C","#D8BFD8","#40E0D0","#F5DEB3","#F5F5F5","#9ACD32"];
    return colors[Math.floor(Math.random()*(colors.length-1))];
}