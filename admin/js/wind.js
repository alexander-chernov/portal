function disableA()
{
    $('#temno').hide();
}
function enableA()
{
    $('#temno').show();
}
function CloseWindow(wid) {
    $('#'+wid).slideUp(500, function(){
        disableA();
    });
}
String.prototype.strip = function() {
    return this.replace(/^\s+/, '').replace(/\s+$/, '');
};
function SearchOpen() {
    $('#lupa_minus').show();
    $('#lupa_plus').hide();
    $('#parametr_search').slideDown(500);
}
function SearchClose() {
    $('#lupa_minus').hide();
    $('#lupa_plus').show();
    $('#parametr_search').slideUp(500);
}