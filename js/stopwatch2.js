// * Stopwatch class {{{
Stopwatch = function (listener, countUp, resolution) {
	this.startTime = 0;
	this.stopTime = 0;
	this.totalElapsed = 0; // * elapsed number of ms in total
	this.initialElapsed = 0;
	this.started = false;
	this.listener = (listener != undefined ? listener : null); // * function to receive onTick events
	this.countUp = countUp;
	this.tickResolution = (resolution != undefined ? resolution : 500); // * how long between each tick in milliseconds
	this.tickInterval = null;
	
	
	this.onehour = 1000 * 60 * 60;
	this.onemin = 1000 * 60;
	this.onesec = 1000;
};
Stopwatch.prototype.start = function () {
	var delegate = function (that, method) { return function () { return method.call(that); }; };
	if (!this.started) {
		this.startTime = new Date().getTime();
		this.stopTime = 0;
		this.started = true;
		this.tickInterval = setInterval(delegate(this, this.onTick), this.tickResolution);
	}
};
Stopwatch.prototype.stop = function () {
	if (this.started) {
		this.stopTime = new Date().getTime();
		this.started = false;
		var elapsed = this.stopTime - this.startTime;
		this.totalElapsed += elapsed;
		if (this.tickInterval != null) {
			clearInterval(this.tickInterval);
		}
	}
	return this.getElapsed();
};
Stopwatch.prototype.reset = function () {
	this.totalElapsed = 0;
	this.startTime = new Date().getTime();
	this.stopTime = this.startTime;
	if (!this.countUp) {
		this.totalElapsed = this.initialElapsed;
	}
	if (this.tickInterval != null) {
		var delegate = function (that, method) { return function () { return method.call(that); }; };
		clearInterval(this.tickInterval);
		this.tickInterval = setInterval(delegate(this, this.onTick), this.tickResolution);
	}
};
Stopwatch.prototype.restart = function () {
	this.stop();
	this.reset();
	this.start();
};
Stopwatch.prototype.getElapsed = function () {
	var elapsed = 0;
	if (this.started) {
		elapsed = new Date().getTime() - this.startTime;
	}
	elapsed += this.totalElapsed;
	if (!this.countUp) {
		elapsed = Math.max(2 * this.initialElapsed - elapsed, 0);
	}
	
	var hours = parseInt(elapsed / this.onehour);
	elapsed %= this.onehour;
	var mins = parseInt(elapsed / this.onemin);
	elapsed %= this.onemin;
	var secs = parseInt(elapsed / this.onesec);
	var ms = elapsed % this.onesec;
	
	return {
		hours: hours,
		minutes: mins,
		seconds: secs,
		milliseconds: ms
	};
};
Stopwatch.prototype.setElapsed = function (hours, mins, secs) {
	this.totalElapsed = 0;
	this.startTime = new Date().getTime();
	this.stopTime = this.startTime;
	this.totalElapsed += hours * this.onehour;
	this.totalElapsed += mins * this.onemin;
	this.totalElapsed += this.countUp ? secs * this.onesec : (secs + 1) * this.onesec - 1;
	this.totalElapsed = Math.max(this.totalElapsed, 0);
	this.initialElapsed = this.totalElapsed;
	if (this.tickInterval != null) {
		var delegate = function (that, method) { return function () { return method.call(that); }; };
		clearInterval(this.tickInterval);
		this.tickInterval = setInterval(delegate(this, this.onTick), this.tickResolution);
	}
};
Stopwatch.prototype.toString = function () {
	var zpad = function (no, digits) {
		no = no.toString();
		while (no.length < digits)
			no = '0' + no;
		return no;
	};
	var e = this.getElapsed();
	return zpad(e.minutes, 2) + ":" + zpad(e.seconds, 2) + ":" + zpad(e.milliseconds, 3);
};
Stopwatch.prototype.setListener = function (listener) {
	this.listener = listener;
};
// * triggered every ms
Stopwatch.prototype.onTick = function () {
	if (this.listener != null) {
		this.listener(this);
	}
};
// }}}