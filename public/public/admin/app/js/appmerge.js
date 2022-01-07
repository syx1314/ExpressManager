function install_merge_loading(type) {
    if (isdownloadPay) {
        alert(askBrowserAlert);
        return;
    }

    if (type == 'ios') {
        if (!isMobileRequest) {
            alert(askBrowserAlert);
            return;
        }
    }

    if (aType == 'ios' && browseType == 'android') {
        alert(forIosAlert);
        return;
    } else if (aType == 'android' && browseType == 'ios') {
//        alert(forAndroidAlert);
//        return;
    }

    if ( isWechatRequest) {
        alert(reminderWechatContent);
        return;
    }
    if ( isWeiboRequest) {
        alert(reminderWeiboContent);
        return;
    }

    if (isQQRequest && aType == 'android') {
        alert(reminderQQContent);
        return ;
    }

    url = "/app/install/" + androidAKey;
    window.location.href = url;
}

