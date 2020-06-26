function gmailEventListener(e) {
    let activeElement = document.activeElement;
    // console.log(e);

    if (e.code === 'Escape') {
        activeElement.blur();
    } else if (e.code === 'KeyI' && e.altKey) {
        gmailScreenIn(activeElement, e);
    } else if (e.code === 'KeyO' && e.altKey) {
        gmailScreenOut(activeElement, e);
    } else if (e.code === 'KeyF' && e.altKey) {
        gmailScreenInFeed(activeElement, e);
    } else if (e.code === 'KeyP' && e.altKey) {
        gmailScreenInPaperTrail(activeElement, e);
    }
}

function gmailScreenIn(activeElement, e) {
    screenMessage('Inbox');
    e.preventDefault();
}

function gmailScreenInFeed(activeElement, e) {
    screenMessage('Feed');
    e.preventDefault();
}

function gmailScreenInPaperTrail(activeElement, e) {
    screenMessage('Paper Trail');
    e.preventDefault();
}

function gmailScreenOut(activeElement, e) {
    screenMessage('Screened Out');
    e.preventDefault();
}

function screenMessage(folder) {
    let toolbars = document.querySelectorAll('.G-tF');
    let toolbar = toolbars[toolbars.length - 1];
    triggerMouseDown(toolbar);

    setTimeout(() => {
        triggerMouseDown(toolbar.querySelector('div[data-tooltip="Labels"]'));
        setTimeout(() => {
            triggerMouseDownAndUp(document.querySelector('div[title="' + folder + '"]'));
            setTimeout(() => {
                if (document.querySelector('span[title="Remove label To Screen from this conversation"]')) {
                    document.querySelector('span[title="Remove label To Screen from this conversation"]').click();
                }
                setTimeout(() => {
                    document.querySelector('div.bBe[role=button]').click();
                    ajaxRequest(folder);
                }, 250);
            }, 250);
        }, 250);
    }, 250);
}

function ajaxRequest(folder) {
    let message = document.querySelector('div[data-message-id]');
    let emailElement = message.querySelector('span[email]');
    let email = emailElement.getAttribute('email');

    let url = 'http://local.heygmail.kalenjordan.com/api/screen-message?' +
        + 'email=' + encodeURIComponent(email)
        + '&folder=' + folder;

    let bgColor = '';
    let color = '';
    if (folder === 'Screened Out') {
        bgColor = '#ce8181';
        color = 'white';
    } else if (folder === 'Inbox') {
        bgColor = '#92c592';
        color = 'white';
    } else if (folder === 'Paper Trail') {
        bgColor = '#b6cff5';
        color = '#0d3472';
    } else if (folder === 'Feed') {
        bgColor = '#fdedc1';
        color = '#684e07';
    }

    let status = document.createElement("div");
    status.style = "background-color: " + bgColor + "; color: " + color + "; padding: 7px; font-size: 16px";
    status.classList.add('hey-screening-confirmation');
    status.innerText = folder + '...';
    document.querySelector('body').prepend(status);

    fetch(url).then(
        function (response) {
            if (response.status !== 200) {
                alert("Error: " + response.status);
                return;
            }

            // Examine the text in the response
            response.json().then(function (data) {
                document.querySelector('.hey-screening-confirmation').textContent = data.message;
                setTimeout(() => {
                    document.querySelector('.hey-screening-confirmation').remove();
                }, 2000);
            });
        }
    ).catch(function (err) {
        console.log('Fetch Error :-S', err);
    });
}
