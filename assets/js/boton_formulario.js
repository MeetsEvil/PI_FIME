var x = document.getElementById("frmlogin");
var y = document.getElementById("frmregistrar");
var z = document.getElementById("btnvai");
var textcolor1=document.getElementById("vaibtnlogin");
var textcolor2=document.getElementById("vaibtnregistrar");
textcolor1.style.color="white";
textcolor2.style.color="black";

function registrarvai()
{
x.style.left = "-400px";
y.style.left = "50px";
z.style.left = "110px";
textcolor1.style.color="black";
textcolor2.style.color="white";
}
function loginvai()
{
x.style.left = "50px";
y.style.left = "450px";
z.style.left = "0";
textcolor1.style.color="white";
textcolor2.style.color="black";
}