function disableA()
{
    document.getElementById('temno').style.visibility='hidden';  
}
function enableA()
{
    document.getElementById('temno').style.visibility='visible';  
}
function ShowFormAdminGlavnaia(i) {
  for(a=1;a<=7;a++) {
    var b = document.getElementById('admin_glavnaia_'+a).style.display = 'none';
  }
  document.getElementById('admin_glavnaia_'+i).style.display = 'block';
}
function ShowFormAdminNedvig(i) {
  for(a=1;a<=5;a++) {
    var b = document.getElementById('admin_nedvigim_'+a).style.display = 'none';
  }
  document.getElementById('admin_nedvigim_'+i).style.display = 'block';
}
function ShowFormAdminPhoto(i) {
  for(a=1;a<=3;a++) {
    var b = document.getElementById('admin_photo_'+a).style.display = 'none';
  }
  document.getElementById('admin_photo_'+i).style.display = 'block';
}
function ShowFormAdminExpert(i) {
  for(a=1;a<=3;a++) {
    var b = document.getElementById('admin_expert_'+a).style.display = 'none';
  }
  document.getElementById('admin_expert_'+i).style.display = 'block';
}
function ShowFormAdminBlog(i) {
  for(a=1;a<=3;a++) {
    var b = document.getElementById('admin_blog_'+a).style.display = 'none';
  }
  document.getElementById('admin_blog_'+i).style.display = 'block';
}
function ShowFormAdminWebcam(i) {
  for(a=1;a<=2;a++) {
    var b = document.getElementById('admin_webcam_'+a).style.display = 'none';
  }
  document.getElementById('admin_webcam_'+i).style.display = 'block';
}
function ShowFormAdminJob(i) {
  for(a=1;a<=3;a++) {
    var b = document.getElementById('admin_job_'+a).style.display = 'none';
  }
  document.getElementById('admin_job_'+i).style.display = 'block';
}
function ShowFormAdminCatalog(i) {
  for(a=1;a<=5;a++) {
    var b = document.getElementById('admin_catalog_'+a).style.display = 'none';
  }
  document.getElementById('admin_catalog_'+i).style.display = 'block';
}
function ShowFormAdminMap(i) {
  for(a=1;a<=5;a++) {
    var b = document.getElementById('admin_map_'+a).style.display = 'none';
  }
  document.getElementById('admin_map_'+i).style.display = 'block';
}
