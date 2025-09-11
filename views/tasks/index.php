<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?= yii\widgets\ListView::widget([
        'dataProvider' => $tasks,
        'itemView' => 'item',
        'summary' => '',
    ]);
    ?>
    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">1</a>
            </li>
            <li class="pagination-item pagination-item--active">
                <a href="#" class="link link--page">2</a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">3</a>
            </li>
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
        </ul>
    </div>
</div>
<div class="right-column">
    <div class="right-card black">
        <div class="search-form">
            <form>
                <h4 class="head-card">Категории</h4>
                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <label class="control-label" for="сourier-services">
                            <input type="checkbox" id="сourier-services" checked="">
                            Курьерские услуги</label>
                        <label class="control-label" for="cargo-transportation">
                            <input id="cargo-transportation" type="checkbox">
                            Грузоперевозки</label>
                        <label class="control-label" for="translations">
                            <input id="translations" type="checkbox">
                            Переводы</label>
                    </div>
                </div>
                <h4 class="head-card">Дополнительно</h4>
                <div class="form-group">
                    <label class="control-label" for="without-performer">
                        <input id="without-performer" type="checkbox" checked="">
                        Без исполнителя</label>
                </div>
                <h4 class="head-card">Период</h4>
                <div class="form-group">
                    <label for="period-value"></label>
                    <select id="period-value">
                        <option>1 час</option>
                        <option>12 часов</option>
                        <option>24 часа</option>
                    </select>
                </div>
                <input type="submit" class="button button--blue" value="Искать">
            </form>
        </div>
    </div>
</div>