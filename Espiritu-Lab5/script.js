let transactions = [];

function addTransaction() {
  const description = document.getElementById("description").value;
  const amount = Number(document.getElementById("amount").value);
  const type = document.getElementById("type").value;

  if (!description || isNaN(amount) || amount <= 0) {
    alert("Please enter valid description and amount");
    return;
  }

  // Create transaction object
  const transaction = {
    description,
    amount,
    type
  };

  transactions.push(transaction);

  // Clear inputs
  document.getElementById("description").value = "";
  document.getElementById("amount").value = "";

  updateUI();
}

function updateUI() {
  let totalIncome = 0;
  let totalExpense = 0;

  const list = document.getElementById("transactionList");
  list.innerHTML = "";

  transactions.forEach((t) => {
    const li = document.createElement("li");

    if (t.type === "income") {
      totalIncome += t.amount;
      li.textContent = `+ ₱${t.amount} - ${t.description}`;
      li.style.color = "green";
    } else {
      totalExpense += t.amount;
      li.textContent = `- ₱${t.amount} - ${t.description}`;
      li.style.color = "red";
    }

    list.appendChild(li);
  });

  const balance = totalIncome - totalExpense;

  document.getElementById("totalIncome").innerText = totalIncome.toFixed(2);
  document.getElementById("totalExpense").innerText = totalExpense.toFixed(2);
  document.getElementById("balance").innerText = balance.toFixed(2);
}