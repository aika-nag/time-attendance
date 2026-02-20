function set2fig(num) {
    let digits;
    if (num < 10) {
        digits = "0" + num;
    } else {
        digits = num;
    }
    return digits;
}
function showClock() {
    let nowTime = new Date();
    let nowHour = set2fig(nowTime.getHours());
    let nowMinute = set2fig(nowTime.getMinutes());
    let message = nowHour + ":" + nowMinute;
    document.getElementById("time").innerText = message;
}
setInterval('showClock()', 1000);
