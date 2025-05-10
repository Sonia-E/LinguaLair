/*
	selectFile.js v1.0
	(c) 2017 by Thielicious
	
	A JavaScript function which lets you customize the browse button and its selection text. 
	This function simply emulates the browse button using an ordinary input button as a trigger.
*/

const selectFile = function() {

	let regex = /[^\\]+$/

	this.choose,
	this.selected

	this.msg = str => {
		let prefix = '[selectFile.js]\n\nError: '
		return alert(prefix + str)
	}

	this.check = () => {
		if (this.choose && this.selected != null) {
			let choose = document.getElementById(this.choose),
				selected = document.getElementById(this.selected);
			if (choose) {
				choose.addEventListener('change', () => {
					if (choose.value != '') {
						selected.innerHTML = choose.value.match(regex);
					} else {
						selected.innerHTML = 'No file selected';
					}
				})
			} else {
				this.msg('Could not find element ' + this.choose);
			}

		} else {
			this.msg('Targets not set.')
		}
	}

	selectFile.prototype.targets = (triggerId, filetextId) => {
		this.choose = triggerId;
		this.selected = filetextId;
		this.check();
	}

	selectFile.prototype.simulate = () => {
		if (this.choose != null) {
			let chooseButton = document.getElementById(this.choose + "_button"); //find button
			let choose = document.getElementById(this.choose); // find input
			if (typeof choose != 'undefined' && chooseButton ) { //check if chooseButton exists
				chooseButton.addEventListener('click', () => choose.click()); // add click
			} else {
				this.msg('Could not find element ' + this.choose);
			}
		} else {
			this.msg('Targets not set.')
		}
	}
};

window.onload = function() { //enclose the script in window.onload
	var getProfile = new selectFile;
	getProfile.targets('profile_pic', 'selected_profile');
	getProfile.simulate();

	var getBgPic = new selectFile;
	getBgPic.targets('bg_pic', 'selected_bg');
	getBgPic.simulate();
}