<?php
// file: src/todo/views/matrix.php
namespace todo;

use bravedave\dvc\strings;  ?>

<div class="container p-4" id="<?= $_container = strings::rand() ?>"></div>
<script>
  (_ => {
    const container = $('#<?= $_container ?>');

    const addControl = () => {

      /*--- ---[add new todo]--- ---*/
      const control = $(`<div class="row g-2">
          <div class="col">
            <div class="input-group">
              <input type="text" class="form-control js-new-todo"
                name="description" placeholder="new todo">
              <div class="input-group-text">
                <i class="bi bi-plus-lg js-indication"></i>
              </div>
            </div>
          </div>
        </div>`)
        .appendTo(container);

      control.find('input').on('change', function(e) {

        if ('' != this.value) {

          const data = {
            action: 'todo-add',
            description: this.value
          };

          $(this).parent().find('.js-indication')
            .removeClass('bi-plus-lg')
            .addClass('spinner-border spinner-border-sm');

          _.fetch.post(_.url('<?= $this->route ?>'), data)
            .then(d => {

              if ('ack' == d.response) {

                _.fetch
                  .post(_.url('<?= $this->route ?>'), {
                    action: 'todo-get-by-id',
                    id: d.id
                  }).then(d => {

                    if ('ack' == d.response) {

                      newRow(d.dto).insertBefore(control);
                      $(this).parent().find('.js-indication')
                        .removeClass('spinner-border spinner-border-sm')
                        .addClass('bi-plus-lg');
                      this.value = '';
                    } else {

                      _.growl(d)
                    }
                  });
              } else {

                _.growl(d);
              }
            });
        }
      });

      control.focus();
      /*--- ---[/add new todo]--- ---*/
    };

    const editItem = function(e) {

      _.hideContexts(e);

      const dto = $(this).closest('div.js-todo').data('dto');

      const fld = $(`<input type="text" class="form-control" data-id="${dto.id}">`)
        .val(dto.description)
        .on('keypress', function(e) {

          if (27 == e.keyCode) {

            e.stopPropagation();
            $(this).trigger('refresh'); // will bubble to the row
          }
        })
        .on('change', function(e) {
          e.stopPropagation();

          let data = {
            action: 'todo-update',
            id: this.dataset.id,
            description: this.value
          };

          _.fetch
            .post(_.url('<?= $this->route ?>'), data)
            .then(d => {

              if ('ack' == d.response) {

                $(this).trigger('refresh'); // will bubble to the row
              } else {

                _.growl(d);
              }
            });
        });

      $(this).empty().append(fld);
      fld.focus();
    };

    const getMatrix = () => new Promise(resolve => {

      _.fetch
        .post(_.url('<?= $this->route ?>'), {
          action: 'todo-get-matrix'
        }).then(d => 'ack' == d.response ? resolve(d.data) : _.growl(d));
    });

    const matrix = data => {

      container.empty().append('<h4><?= config::label ?></h4>');

      $.each(data, (i, dto) => newRow(dto).appendTo(container));
      addControl(); // adds the controls to add an item
    };

    const newRow = dto => {

      const row = $(
          `<div class="row g-2 mb-2 js-todo" data-id="${dto.id}">
            <div class="col js-description">
              <div class="p-2">${dto.description}</div>
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-light js-delete">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>`)
        .data('dto', dto)
        .on('delete', function(e) {

          _.hideContexts(e);

          _.ask.alert.confirm({
            title: 'Confirm Delete',
            text: 'Are you sure ?'
          }).then(e => {

            _.fetch
              .post(_.url('<?= $this->route ?>'), {
                action: 'todo-delete',
                id: this.dataset.id
              }).then(d => 'ack' == d.response ? this.remove() : _.growl(d));
          });
        })
        .on('refresh', function(e) {

          _.fetch
            .post(_.url('<?= $this->route ?>'), {
              action: 'todo-get-by-id',
              id: this.dataset.id
            }).then(d => {

              if ('ack' == d.response) {

                const row_ = $(this);
                row_.find('.js-description')
                  .empty()
                  .append(`<div class="p-2">${d.dto.description}</div>`)
                  .addClass('pointer')
                  .off('click')
                  .one('click', editItem);
                row_.data('dto', d.dto);
              } else {

                _.growl(d)
              }
            });
        });

      // note, using one instead of on to avoid multiple handlers, once activated that's it
      row.find('.js-description')
        .addClass('pointer')
        .one('click', editItem);

      row.find('.js-delete').on('click', function(e) {
        e.stopPropagation();

        $(this).closest('div.js-todo').trigger('delete');
        this.innerHTML = '<div class="spinner-grow spinner-grow-sm"></div>';
      });

      return row;
    };

    _.ready(() => getMatrix().then(matrix));
  })(_brayworth_);
</script>