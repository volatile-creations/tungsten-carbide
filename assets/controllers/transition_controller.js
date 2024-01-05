import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['subject'];
    static classes = [
        'entering',
        'enteringFrom',
        'enteringTo',

        'leaving',
        'leavingFrom',
        'leavingTo'
    ];
    static values = {
        entered: Boolean
    };

    toggle(event) {
        event.preventDefault();
        this.enteredValue = !this.enteredValue;
    }

    enteredValueChanged() {
        if (this.enteredValue) {
            this.enter();
            return;
        }

        this.leave();
    }

    enter() {
        const { classList } = this.subjectTarget;

        classList.remove(
            ...this.leavingToClasses,
            ...this.leavingClasses
        );
        classList.add(...this.enteringFromClasses);
        classList.add(...this.enteringClasses);

        classList.remove(...this.enteringFromClasses);
        classList.add(...this.enteringToClasses);
    }

    leave () {
        const { classList } = this.subjectTarget;

        classList.remove(
            ...this.enteringToClasses,
            ...this.enteringClasses
        );
        classList.add(...this.leavingFromClasses);
        classList.add(...this.leavingClasses);

        classList.remove(...this.leavingFromClasses);
        classList.add(...this.leavingToClasses);
    }
}
