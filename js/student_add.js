//学生番号を手入力させるので、半角英数字になるようにしておきたい
const student_no = document.getElementById('student_no');

// 入力されるたびに数字以外を取り除く
student_no.addEventListener("input", () => {
    student_no.value = student_no.value.replace(/\D/g, "")
})