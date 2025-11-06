// admin/js/index.js
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const duration = 2000; // durasi animasi dalam milidetik
        const frameRate = 60; // fps
        const totalFrames = Math.round(duration / (1000 / frameRate));
        let frame = 0;

        const countUp = () => {
            frame++;
            const progress = frame / totalFrames;
            const currentCount = Math.round(target * progress);
            counter.innerText = currentCount;

            if(frame < totalFrames) {
                requestAnimationFrame(countUp);
            } else {
                counter.innerText = target;
            }
        };

        countUp();
    });
});
