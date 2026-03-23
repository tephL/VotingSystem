
const rules = {
  president: 1,
  vice_mayor: 1,
  senator: 2
};

function disableButton(name, el) {
  const max = rules[name];
  const boxes = document.getElementsByName(name);

  let checkedcount = 0;

  // count checked
  for (let i = 0; i < boxes.length; i++) {
    if (boxes[i].checked) checkedcount++;
  }

  // if user tries to exceed limit
  if (checkedcount > max) {
    el.checked = false;
    return;
  }
  
  //reset
  for (let i = 0; i < boxes.length; i++) {
    boxes[i].disabled = false;
  }

  // apply limit
  if (checkedcount >= max) {
    for (let i = 0; i < boxes.length; i++) {
      if (!boxes[i].checked) {
        boxes[i].disabled = true;
      }
    }
  }
}


