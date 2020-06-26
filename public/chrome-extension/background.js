let version = 0.4;
console.log("KJ Keyboard Shortcuts v" + version + " (domain: " + document.domain + ")");
console.log(document.domain);

document.addEventListener('keydown', function (e) {
    if (document.domain === 'twitter.com') {
        twitterEventListener(e);
    } else if (document.domain === 'roamresearch.com') {
        roamEventListener(e);
    } else if (document.domain === 'app.youneedabudget.com') {
        ynabEventListener(e);
    } else if (document.domain === 'mail.google.com') {
        gmailEventListener(e);
    }
});

function triggerMouseDown(element)
{
    let evt = document.createEvent('MouseEvents');
    evt.initMouseEvent('mousedown', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
    element.dispatchEvent(evt);
}

function triggerMouseUp(element)
{
    let evt = document.createEvent('MouseEvents');
    evt.initMouseEvent('mouseup', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
    element.dispatchEvent(evt);
}

function triggerMouseDownAndUp(selector)
{
    triggerMouseDown(selector);
    setTimeout(() => {
        triggerMouseUp(selector);
    }, 200);
}

function notTyping() {
    return document.activeElement.tagName !== 'INPUT';
}
