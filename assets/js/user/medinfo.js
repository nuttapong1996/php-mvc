document.addEventListener('DOMContentLoaded', async () => {
    const txBloodType = document.getElementById('txBloodType');
    const txFoodAllergy = document.getElementById('txFoodAllergy');
    const txDrugAllergy = document.getElementById('txDrugAllergy');
    const txAnimalsAllergy = document.getElementById('txAnimalsAllergy');

    try {
        const medRes = await fetch('user/medinfo', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
        });

        if (!medRes.ok) {
            throw new Error('Error fetch data : ' + medRes.status);
        }
        const data = await medRes.json();


        if (data.data.name_blood_type == null) {
            txBloodType.innerHTML = 'ไม่ระบุกรุ๊ปเลือด';
        } else {
            txBloodType.innerHTML = data.data.name_blood_type;
        }

        if (data.data.food_allergy == 'n') {
            txFoodAllergy.innerHTML = 'ไม่มี';
        } else {
            txFoodAllergy.innerHTML = data.data.food_detail_allergy;

        }

        if (data.data.drug_allergy == 'n') {
            txDrugAllergy.innerHTML = 'ไม่มี';
        } else {
            txDrugAllergy.innerHTML = data.data.drug_detail_allergy;

        }


        if (data.data.animals_allergy == 'n') {
            txAnimalsAllergy.innerHTML = 'ไม่มี';
        } else {
            txAnimalsAllergy.innerHTML = data.data.animals_detail_allergy;
        }
    } catch (error) {
        console.error(error);
        txBloodType.innerHTML = '-';
        txFoodAllergy.innerHTML = '-';
        txDrugAllergy.innerHTML = '-';
        txAnimalsAllergy.innerHTML = '-';
    }
})