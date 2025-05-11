const daysPassedYear = document.querySelector('.days-passed-year');
const percentYearPassed = document.querySelector('.percent-year-passed');
const meterYear = document.querySelector('.meter--year');

const dayOfMonthElement = document.querySelector('.day-of-month');
const percentMonthPassed = document.querySelector('.percent-month-passed');
const meterMonth = document.querySelector('.meter--month');

const daysPassedWeek = document.querySelector('.days-passed-week');
const percentWeekPassed = document.querySelector('.percent-week-passed');
const meterWeek = document.querySelector('.meter--week');

const currentTimeElement = document.querySelector('.current-time');
const percentDayPassed = document.querySelector('.percent-day-passed');
const meterDay = document.querySelector('.meter--day');
const dayOfWeekElement = document.querySelector('.day-of-week');
const monthNameElement = document.querySelector('.month-name');
const monthDay = document.querySelector('.month-day');

function updateTimePercentages() {
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const day = currentDate.getDate();
    const hour = currentDate.getHours();
    const minute = currentDate.getMinutes();
    const second = currentDate.getSeconds();
    const dayOfWeek = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
    const monthName = currentDate.toLocaleDateString('en-US', { month: 'long' });
    const currentTimeFormatted = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:${String(second).padStart(2, '0')}`;

    // Year
    const isLeapYear = year % 100 === 0 ? year % 400 === 0 : year % 4 === 0;
    const daysInYear = isLeapYear ? 366 : 365;
    const startOfYear = new Date(year, 0, 1);
    const daysPassedYearValue = Math.ceil((currentDate - startOfYear) / 86400000);
    const daysPassedYearFormatted = daysPassedYearValue;
    const percentYearPassedValue = Math.round((daysPassedYearValue / daysInYear) * 100);
    daysPassedYear.innerText = daysPassedYearFormatted;
    percentYearPassed.innerText = percentYearPassedValue + '%';
    meterYear.value = percentYearPassedValue;

    // Month
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const percentMonthPassedValue = Math.round((day / daysInMonth) * 100);
    dayOfMonthElement.innerText = day + '/' + daysInMonth;

    if (String(day).endsWith('1') && day !== 11) {
    monthDay.innerText = ' ' + day + 'st';
    } else if(String(day).endsWith('2') && day !== 12) {
        monthDay.innerText = ' ' + day + 'nd';
    } else if(String(day).endsWith('3') && day !== 13) {
        monthDay.innerText = ' ' + day + 'rd';
    } else {
        monthDay.innerText = ' ' + day + 'th';
    }
    
    percentMonthPassed.innerText = percentMonthPassedValue + '%';
    meterMonth.value = percentMonthPassedValue;

    // Week
    const currentDayOfWeekNumber = currentDate.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
    const daysPassedWeekValue = currentDayOfWeekNumber === 0 ? 7 : currentDayOfWeekNumber; // Treat Sunday as the end
    const percentWeekPassedValue = Math.round((daysPassedWeekValue / 7) * 100);
    const daysPassedWeekFormatted = daysPassedWeekValue;
    daysPassedWeek.innerText = daysPassedWeekFormatted;
    percentWeekPassed.innerText = percentWeekPassedValue + '%';
    meterWeek.value = percentWeekPassedValue;

    // Day
    const totalSecondsInDay = 24 * 60 * 60;
    const secondsPassedDayValue = hour * 3600 + minute * 60 + second;
    const percentDayPassedValue = Math.round((secondsPassedDayValue / totalSecondsInDay) * 100);
    currentTimeElement.innerText = currentTimeFormatted;
    percentDayPassed.innerText = percentDayPassedValue + '%';
    meterDay.value = percentDayPassedValue;

    // Update H2
    dayOfWeekElement.innerText = dayOfWeek;
    monthNameElement.innerText = monthName;
    currentTimeElement.innerText = currentTimeFormatted;
}

// Update the percentages immediately when the page loads
updateTimePercentages();

// Update the day percentage and time every second
setInterval(updateTimePercentages, 1000);


const aboutBtn = document.getElementById('about-btn')

aboutBtn.addEventListener('click', () => {
  window.location.href = "about";
});

const faqBtn = document.getElementById('faq-btn')

faqBtn.addEventListener('click', () => {
  window.location.href = "FAQ";
});

const contactBtn = document.getElementById('contact-btn')

contactBtn.addEventListener('click', () => {
  window.location.href = "contact";
});