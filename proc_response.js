function Response_Islem(_operation) {
    switch (_operation) {
        case 'sign_in':
            window.location.href = 'page_account.php';
            break;
        case 'logout':
            window.location.href = 'page_login.php';
            break;

        default:
            break;
    }
}