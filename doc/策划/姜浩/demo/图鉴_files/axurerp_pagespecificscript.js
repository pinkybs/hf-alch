
var PageName = '图鉴';
var PageId = 'pf8bcce43dbf1493090700c786cffe397'
var PageUrl = '图鉴.html'
document.title = '图鉴';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

var $OnLoadVariable = '';

var $CSUM;

var hasQuery = false;
var query = window.location.hash.substring(1);
if (query.length > 0) hasQuery = true;
var vars = query.split("&");
for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0].length > 0) eval("$" + pair[0] + " = decodeURIComponent(pair[1]);");
} 

if (hasQuery && $CSUM != 1) {
alert('Prototype Warning: The variable values were too long to pass to this page.\nIf you are using IE, using Firefox will support more data.');
}

function GetQuerystring() {
    return '#OnLoadVariable=' + encodeURIComponent($OnLoadVariable) + '&CSUM=1';
}

function PopulateVariables(value) {
  value = value.replace(/\[\[OnLoadVariable\]\]/g, $OnLoadVariable);
  value = value.replace(/\[\[PageName\]\]/g, PageName);
  return value;
}

function OnLoad(e) {

}

var u20 = document.getElementById('u20');
gv_vAlignTable['u20'] = 'center';
var u36 = document.getElementById('u36');
gv_vAlignTable['u36'] = 'top';
var u31 = document.getElementById('u31');
gv_vAlignTable['u31'] = 'top';
var u11 = document.getElementById('u11');

var u27 = document.getElementById('u27');

var u6 = document.getElementById('u6');

var u4 = document.getElementById('u4');

var u2 = document.getElementById('u2');

var u10 = document.getElementById('u10');
gv_vAlignTable['u10'] = 'center';
var u0 = document.getElementById('u0');

var u26 = document.getElementById('u26');
gv_vAlignTable['u26'] = 'center';
var u35 = document.getElementById('u35');
gv_vAlignTable['u35'] = 'top';
var u29 = document.getElementById('u29');

u29.style.cursor = 'pointer';
if (bIE) u29.attachEvent("onclick", Clicku29);
else u29.addEventListener("click", Clicku29, true);
function Clicku29(e)
{

if (true) {

	self.location.href="工作台.html" + GetQuerystring();

}

}
gv_vAlignTable['u29'] = 'top';
var u8 = document.getElementById('u8');
gv_vAlignTable['u8'] = 'top';
var u34 = document.getElementById('u34');
gv_vAlignTable['u34'] = 'top';
var u14 = document.getElementById('u14');
gv_vAlignTable['u14'] = 'center';
var u28 = document.getElementById('u28');
gv_vAlignTable['u28'] = 'center';
var u44 = document.getElementById('u44');
gv_vAlignTable['u44'] = 'top';
var u33 = document.getElementById('u33');
gv_vAlignTable['u33'] = 'top';
var u22 = document.getElementById('u22');
gv_vAlignTable['u22'] = 'center';
var u13 = document.getElementById('u13');

var u12 = document.getElementById('u12');
gv_vAlignTable['u12'] = 'center';
var u41 = document.getElementById('u41');

u41.style.cursor = 'pointer';
if (bIE) u41.attachEvent("onclick", Clicku41);
else u41.addEventListener("click", Clicku41, true);
function Clicku41(e)
{

if (true) {

	self.location.href="工作台.html" + GetQuerystring();

}

}

var u21 = document.getElementById('u21');

var u37 = document.getElementById('u37');
gv_vAlignTable['u37'] = 'top';
var u7 = document.getElementById('u7');
gv_vAlignTable['u7'] = 'center';
var u40 = document.getElementById('u40');
gv_vAlignTable['u40'] = 'top';
var u17 = document.getElementById('u17');

var u5 = document.getElementById('u5');
gv_vAlignTable['u5'] = 'center';
var u15 = document.getElementById('u15');

var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'center';
var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u25 = document.getElementById('u25');

var u43 = document.getElementById('u43');
gv_vAlignTable['u43'] = 'center';
var u16 = document.getElementById('u16');
gv_vAlignTable['u16'] = 'center';
var u39 = document.getElementById('u39');
gv_vAlignTable['u39'] = 'top';
var u19 = document.getElementById('u19');

var u9 = document.getElementById('u9');

var u30 = document.getElementById('u30');

u30.style.cursor = 'pointer';
if (bIE) u30.attachEvent("onclick", Clicku30);
else u30.addEventListener("click", Clicku30, true);
function Clicku30(e)
{

if (true) {

	self.location.href="材料.html" + GetQuerystring();

}

}
gv_vAlignTable['u30'] = 'top';
var u24 = document.getElementById('u24');
gv_vAlignTable['u24'] = 'center';
var u38 = document.getElementById('u38');
gv_vAlignTable['u38'] = 'top';
var u18 = document.getElementById('u18');
gv_vAlignTable['u18'] = 'center';
var u32 = document.getElementById('u32');
gv_vAlignTable['u32'] = 'top';
var u42 = document.getElementById('u42');

var u23 = document.getElementById('u23');

if (window.OnLoad) OnLoad();
