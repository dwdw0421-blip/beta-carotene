/**
 * アラートを表示して、指定のページへ遷移する
 * @param {string} message - 表示するメッセージ
 * @param {string} url - 遷移先のパス
 */
function showErrorAlert(message, url) {
    alert(message);
    if (url) {
        window.location.href = url;
    }
}