function doBubble(anz) 
{
    if(anz > 0)
        Notificon(anz); 
    else 
        removeNotificon(); 
}

/**
 * Dateianhänge Anzeigen/Ausblenden
 * 
 * Wird verwendet um (bei einem Post, neuem Thema etc) das Feld für die Dateianhänge anzuzeigen oder auszublenden. 
 * 
 * @author s-l 
 * @version 0.0.1 
 */
function toggledateianhaenge() 
{
    var object = document.getElementById("ckeditor_dateiupload"); 
    if(object.style.display == "none") 
    {
        object.style.display = "block"; 
    }
    else
    {
        object.style.display = "none"; 
    }
}

$(document).ready(function() {
    $("#ckup_form").submit(function() {
        
        alert("submit"); 

        return false; 
    });

    $("#ckup_button").click(function() {
        $("#ckup_file").click(); 
        $("#ckup_form").submit(); 
    });

});

/**
 * Thema Bearbeiten (Select-Box) Tag setzen
 * 
 * Beim bearbeiten eines Themas wird eine Select-Box für den Tag des Themas (falls vorhanden) angezeigt. Wenn man ein Thema bearbeitet
 * das bereits einen Tag hat soll durch diese Funktion der Tag automatisch in die Select-Box übernommen werden. 
 * 
 * @param {*int} Tag-ID 
 * @author s-l 
 * @version 0.0.1 
 */
function setEditThemeSelectBoxTag(tag) 
{
    document.getElementById("selectbox_edit_theme").value = tag; 
}

/**
 * Nach oben Scrollen
 * 
 * Wird aufgerufen wenn der Button im Footer (Pfeil nach oben) angeklickt wird um die Seite nach oben zu Scrollen. 
 * 
 * @author s-l 
 * @version 0.0.1 
 */
function scrollToTop() {
    $('html, body').animate({scrollTop:0}, 'slow');
} 


/**
 * Online Status
 * 
 * Sorgt dafür das (solange das Browser-Tab geöffnet ist) der Online-Status des Benutzers aktualisiert wird
 * 
 * @author s-l 
 * @version 0.0.1 
 * @param {*int} user
 */
function setOnlineStatus(user) 
{
    $.ajax({
        url:"data/ajax/stayOnline.php",
        data:"user="+user,
        type:"POST",
        success:function(msg) {
            window.setTimeout(setOnlineStatus(user), 10000); 
        }
    });
}


function startCheckAlerts(user) 
{
    checkNewAlerts(user); 
}
function checkNewAlerts(user) 
{
    var alerts = 0; 
    $.ajax({
        url:"data/ajax/checkNewAlerts.php",
        data:"user="+user,
        type:"POST",
        success:function(msg) {
            if(msg != "") 
                alerts = alerts + parseInt(msg); 
            checkNewMsgs(user, alerts); 
        }
    });
}
function checkNewMsgs(user, alerts) 
{
    $.ajax({
        url:"data/ajax/checkNewMsg.php",
        data:"user="+user,
        type:"POST",
        success:function(msg) {
            if(msg != "") 
                alerts = alerts + parseInt(msg); 
            doBubbleUpdate(alerts); 
            window.setTimeout(checkNewAlerts(user), 10000); 
        }
    });
}

function doBubbleUpdate(alerts) 
{
    doBubble(alerts); 
}

function showUserProfileCommentID(cid) 
{
    var obj = document.getElementById("uprofile_answer_" + cid); 
    if(obj.style.display == "none") 
    {
        obj.style.display = "block"; 
    }
    else
    {
        obj.style.display = "none"; 
    }
}