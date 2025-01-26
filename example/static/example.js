// Set up a dumb AJAX request to add two numbers.

function addNumbers(a, b) {
    let xhr = new XMLHttpRequest()
    xhr.open('POST', '/add', true)
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('sum').innerHTML = xhr.responseText
        }
    }
    xhr.send('num1=' + a + '&num2=' + b)
}

document.getElementById('add').addEventListener('click', function () {
    let a = parseFloat(document.getElementById('num1').value)
    let b = parseFloat(document.getElementById('num2').value)
    addNumbers(a, b)
})