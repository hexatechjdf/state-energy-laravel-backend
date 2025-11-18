function waitElement(selector) {
    return new Promise(function (resolve) {
        let elm = document.querySelector(selector);

        if (elm) {
            resolve(elm);
            return;
        }

        new MutationObserver(function () {
            elm = document.querySelector(selector);
            if (elm) {
                this.disconnect();
                resolve(elm);
            }
        }).observe(document, { subtree: true, childList: true });
    });
}

waitElement(".thank-you-message").then(() => {
    const storedData = localStorage.getItem("_ud");
    let parsedData = null;

    try {
        parsedData = storedData ? JSON.parse(storedData) : null;
    } catch (e) {
        parsedData = storedData;
    }

    const payload = {
        event: "form_completed",
        data: parsedData
    };

    window.parent.postMessage(payload, "*");
});
