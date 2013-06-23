var bustcachevar=1 
var loadstatustext="<div align='center'><img src='includes/ajaxtabs/loading.gif' /><br /><a style='color:FFF;'>Requesting content...</a></div>"

var loadedobjects=""
var defaultcontentarray=new Object()
var bustcacheparameter=""

function ajaxpage(url, containerid, targetobj){
var page_request = false
if (window.XMLHttpRequest)
page_request = new XMLHttpRequest()
else if (window.ActiveXObject){
try {
page_request = new ActiveXObject("Msxml2.XMLHTTP")
} 
catch (e){
try{
page_request = new ActiveXObject("Microsoft.XMLHTTP")
}
catch (e){}
}
}
else
return false
var ullist=targetobj.parentNode.parentNode.getElementsByTagName("li")
for (var i=0; i<ullist.length; i++)
ullist[i].className=""
targetobj.parentNode.className="selected"
if (url.indexOf("#default")!=-1){
document.getElementById(containerid).innerHTML=defaultcontentarray[containerid]
return
}
document.getElementById(containerid).innerHTML=loadstatustext
page_request.onreadystatechange=function(){
loadpage(page_request, containerid)
}
if (bustcachevar)
bustcacheparameter=(url.indexOf("?")!=-1)? "&"+new Date().getTime() : "?"+new Date().getTime()
page_request.open('GET', url+bustcacheparameter, true)
page_request.send(null)
}

function loadpage(page_request, containerid){
if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1))
document.getElementById(containerid).innerHTML=page_request.responseText
}

function loadobjs(revattribute){
if (revattribute!=null && revattribute!=""){
var objectlist=revattribute.split(/\s*,\s*/)
for (var i=0; i<objectlist.length; i++){
var file=objectlist[i]
var fileref=""
if (loadedobjects.indexOf(file)==-1){
if (file.indexOf(".js")!=-1){
fileref=document.createElement('script')
fileref.setAttribute("type","text/javascript");
fileref.setAttribute("src", file);
}
else if (file.indexOf(".css")!=-1){
fileref=document.createElement("link")
fileref.setAttribute("rel", "stylesheet");
fileref.setAttribute("type", "text/css");
fileref.setAttribute("href", file);
}
}
if (fileref!=""){
document.getElementsByTagName("head").item(0).appendChild(fileref)
loadedobjects+=file+" "
}
}
}
}

function expandtab(tabcontentid, tabnumber){
var thetab=document.getElementById(tabcontentid).getElementsByTagName("a")[tabnumber]
if (thetab.getAttribute("rel")){
ajaxpage(thetab.getAttribute("href"), thetab.getAttribute("rel"), thetab)
loadobjs(thetab.getAttribute("rev"))
}
}

function savedefaultcontent(contentid){
if (typeof defaultcontentarray[contentid]=="undefined")
defaultcontentarray[contentid]=document.getElementById(contentid).innerHTML
}

function startajaxtabs(){
for (var i=0; i<arguments.length; i++){
var ulobj=document.getElementById(arguments[i])
var ulist=ulobj.getElementsByTagName("li")
for (var x=0; x<ulist.length; x++){
var ulistlink=ulist[x].getElementsByTagName("a")[0]
if (ulistlink.getAttribute("rel")){
var modifiedurl=ulistlink.getAttribute("href").replace(/^http:\/\/[^\/]+\//i, "http://"+window.location.hostname+"/")
ulistlink.setAttribute("href", modifiedurl)
savedefaultcontent(ulistlink.getAttribute("rel"))
ulistlink.onclick=function(){
ajaxpage(this.getAttribute("href"), this.getAttribute("rel"), this)
loadobjs(this.getAttribute("rev"))
return false
}
if (ulist[x].className=="selected"){
ajaxpage(ulistlink.getAttribute("href"), ulistlink.getAttribute("rel"), ulistlink)
loadobjs(ulistlink.getAttribute("rev"))
}
}
}
}
}