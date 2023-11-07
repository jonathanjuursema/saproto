const shiftSelect = (el, name) => {
  console.log(`Start shift select for ${name}`);
  let checkboxes = Array.from(el.querySelectorAll(`input[type='checkbox'][name='${name}[]']`));
  let lastCheckbox;

  let shiftSelectAction = (target, e) => {
    console.log('Checkbox clicked');
    if (lastCheckbox && e.shiftKey) {
      let start = checkboxes.findIndex((elem) => elem === lastCheckbox);
      let end = checkboxes.findIndex((elem) => elem === target);
      for (let checkbox of checkboxes.slice(Math.min(start, end), Math.max(start, end) + 1)) {
        checkbox.checked = target.checked;
      }
    }
    lastCheckbox = target;
  };
  for (let checkbox of checkboxes) {
    checkbox.addEventListener('click', (e) => shiftSelectAction(checkbox, e));
    if (checkbox.id) {
      el.querySelector(`label[for=${checkbox.id}]`).addEventListener('click', (e) => {
        if (e.shiftKey) checkbox.checked = !checkbox.checked;
        shiftSelectAction(checkbox, e);
      });
    }
  }
};

export default shiftSelect;
