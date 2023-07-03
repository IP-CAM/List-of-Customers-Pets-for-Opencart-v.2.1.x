<div>


    <div id="tablePetsWrapper" class="<?php echo (!empty($customer_pets)) ? 'show' : 'hide'; ?>">

        <h2>Список питомцев пользователя</h2>

        <table class="table" id="pets-table">
            <thead>
            <tr>
                <th>Питомец</th>
                <th>Порода</th>
                <th>Пол</th>
                <th>Возраст (мес.)</th>
            </tr>
            </thead>
            <tbody>
            <?php if($customer_pets){ ?>
                <?php foreach ($customer_pets as $pet) { ?>
                <tr>
                    <td><?php echo $pets[$pet['pet_type']]['title']; ?></td>
                    <td><?php echo $pet['breed_name']; ?></td>
                    <td><?php echo $pet['gender']; ?></td>
                    <td><?php echo $pet['age_months']; ?></td>
                    <td>
                        <button class="btn-delete" data-pet-id="<?php echo $pet['id']; ?>"><span class="remove"></span></button>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>


    <h2><?php //echo $heading_title . $customer_id; ?></h2>
    <form id="petForm">
        <div class="form-group">
            <label for="petSelect">Питомец:</label>
            <select class="form-control" id="petSelect">
                <option value="">Выберите питомца</option>
                <?php foreach($pets as $key => $value) { ?>
                   <option value="<?php echo $key; ?>"><?php echo $value['title']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group" id="breedContainer" style="display: none;">
            <label for="breedSelect">Порода:</label>
            <select class="form-control" id="breedSelect">
                <option value="">Выберите породу</option>
            </select>
        </div>

        <div class="form-group" id="genderContainer" style="display: none;">
            <label for="genderSelect">Пол:</label>
            <select class="form-control" id="genderSelect">
                <option value="">Выберите пол</option>
                <?php foreach($gender as $key => $value) { ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="ageInput">Возраст в месяцах:</label>
            <input type="number" class="form-control" id="ageInput" min="1" placeholder="Введите возраст" required>
        </div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
</div>


<script>
    const data_pets = <?php echo json_encode($pets); ?>;

    document.addEventListener("DOMContentLoaded", () => {
        addEventClickAllBtnDelete()
        const tablePetsWrapper = document.getElementById('tablePetsWrapper')
        const form = document.getElementById("petForm");
        const petSelect = document.getElementById("petSelect");
        const breedContainer = document.getElementById("breedContainer");
        const breedSelect = document.getElementById("breedSelect");
        const genderContainer = document.getElementById("genderContainer");
        const ageInput = document.getElementById("ageInput");

        const resets = () => {
            breedSelect.innerHTML = ""; // Очищаем список пород

            if (petSelect.value === "") {
                breedContainer.style.display = "none"; // Скрываем контейнер с породами
                return;
            }

            ageInput.value = null
            breedContainer.style.display = "none";
            genderContainer.style.display = "none";
        }

        const populateBreeds = () => {

            resets()

            breedContainer.style.display = "block"; // Показываем контейнер с породами

            const selectedPet = petSelect.value;

            data_pets[selectedPet]['breeds'].forEach(function(breed) {
                const option = document.createElement("option");
                option.text = breed.breed_name;
                option.value = breed.id;
                breedSelect.appendChild(option);
            });
        }
        petSelect.addEventListener('change', populateBreeds)

        petSelect.addEventListener("change", function() {
            if (["turtle", "cat", "dog"].includes(petSelect.value)) {
                genderContainer.style.display = "block";
            } else {
                genderContainer.style.display = "none";
            }
        });

        const handler = (event) => {

            event.preventDefault();

            const formData = {
                pet_type: petSelect.value,
                breed: data_pets[petSelect.value]['breeds'][breedSelect.value]['breed_name'],
                gender: genderSelect.value,
                age: ageInput.value
            };
            console.log(formData)

            $.ajax({
                url: "index.php?route=module/customers_pets/addPetOfCustomer",
                type: "post",
                dataType: "json",
                data: formData,
                success: function(response) {

                    if (response.success && response.pet !== undefined) {

                        addPetRow(response.pet[0]);

                        handleShowHideTablePets()
                        petSelect.value = ""
                        resets()
                    } else {

                        const error = response.error
                        const alertMessage = `Error when adding a pet !!! ${error}`
                        alert(alertMessage);

                        petSelect.value = ""

                        resets()
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });

            resets()
        }

        form.addEventListener("submit", handler);

        const addPetRow = (pet) => {
            const row = $('<tr>');
            row.append($('<td>').text(data_pets[pet.pet_type].title));
            row.append($('<td>').text(pet.breed_name));
            row.append($('<td>').text(pet.gender));
            row.append($('<td>').text(pet.age_months));
            row.append($('<td>').html('<button class="btn-delete" data-pet-id="' + pet.id + '"><span class="remove"></span></button>'));

            $('#pets-table tbody').append(row);

            addEventClickAllBtnDelete()
        }

    });

    const addEventClickAllBtnDelete = () => {

        $('.btn-delete').click(function() {
            const petId = $(this).data('pet-id');
            const currentBtnDelete = $(this)

            $.ajax({
                url: 'index.php?route=module/customers_pets/deletePetOfCustomer',
                type: 'POST',
                data: { pet_id: petId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        currentBtnDelete.closest('tr').remove();

                        handleShowHideTablePets()

                    } else {
                        alert('Ошибка при удалении питомца');
                    }
                },
                error: function() {
                    alert('Ошибка при выполнении запроса');
                }
            });
        });
    }

    const handleShowHideTablePets = () => {
        if ($('.btn-delete').length < 1) {
            $('#tablePetsWrapper').removeClass('show');
            $('#tablePetsWrapper').addClass('hide');
        } else {
            if ($('.hide')[0]) $('#tablePetsWrapper').removeClass('hide');
            if (!$('.show')[0]) $('#tablePetsWrapper').removeClass('show');
        }
    }

</script>
<style>
    .btn-delete {
        display: block;
        width: 25px;
        height: 25px;
        position: relative;
        border: 0px;
        background: transparent;
    }
    .remove {
        position: absolute;
        right: 0px;
        top: 0px;
        width: 18px;
        height: 18px;
        opacity: 0.3;
    }
    .remove:hover {
        opacity: 1;
    }
    .remove:before, .remove:after {
        position: absolute;
        left: 15px;
        content: ' ';
        height: 18px;
        width: 2px;
        background-color: #333;
    }
    .remove:before {
        transform: rotate(45deg);
    }
    .remove:after {
        transform: rotate(-45deg);
    }
</style>

</div>