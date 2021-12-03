import '@material/mwc-icon-button-toggle';

import {type Cash} from 'cash-dom/dist/cash';

import Component from '../Component.jsx';
import Mdi from '../Mdi.jsx';

/**
 * Attributes:
 * - type?: numeric, checkbox
 */
export default class TableColumn extends Component {
  view(vnode) {
    this.attrs.addClassNames('mdc-data-table__header-cell', {
      [`mdc-data-table__header-cell--${this.attrs.get('type')}`]: this.attrs.has('type')
    });

    if (this.attrs.has('sortable')) {
      this.attrs.addClassNames('mdc-data-table__header-cell--with-sort');
      this.attrs.put('aria-sort', 'none').put('data-column-id', this.attrs.get('id'));

      vnode.children = (
        <div className="mdc-data-table__header-cell-wrapper">
          <mwc-icon-button-toggle style="--mdc-icon-button-size: 28px; display: none;">
            <Mdi icon="arrow-down-thin" slot="onIcon"/>
            <Mdi icon="arrow-up-thin" slot="offIcon" />
          </mwc-icon-button-toggle>
          &nbsp;
          <div className="mdc-data-table__header-cell-label">
            {vnode.children}
          </div>
        </div>
      );
    }

    if ((!vnode.children || vnode.children.length === 0) && this.attrs.get('type') === 'checkbox') {
      vnode.children = <mwc-checkbox className="mdc-data-table__header-row-checkbox" />;
    }

    if (this.attrs.get('type') !== 'checkbox' && this.attrs.has('filterable')) {
      vnode.children = (
        <>
          {vnode.children}
          <div style="margin-top: 8px;">
            <text-field outlined className="mdc-data-table__filter-textfield" label={__('Filtro')} compact/>
          </div>
        </>
      );
    }

    return <th {...this.attrs.all()} role="columnheader" scope="col">{vnode.children}</th>;
  }

  oncreate(vnode) {
    super.oncreate(vnode);

    if (this.attrs.get('type') === 'checkbox') {
      window.vnode = $(vnode.dom);
      const checkbox = $(vnode.dom)
        .children('mwc-checkbox');

      checkbox.on('change', () => {
        const row: Cash = $(this.element)
          .closest('table')
          .find('tbody tr[checkable]');

        row.addClass('mdc-data-table__row--selected');

        row.find('mwc-checkbox').prop('checked', checkbox.prop('checked'));
      });
    }

    // Handle click on column (add arrows)
    const observer = new MutationObserver((mutations) => {
      for (const mutation of mutations) {
        const {classList} = mutation.target;
        const ascendingClass = 'mdc-data-table__header-cell--sorted';
        const descendingClass = 'mdc-data-table__header-cell--sorted-descending';

        const onValue = classList.contains(descendingClass);

        const button: Cash = $(this.element).find('mwc-icon-button-toggle');
        button.prop('on', onValue);

        if (classList.contains(ascendingClass) || classList.contains(descendingClass)) {
          $(this.element).css('cursor', 'auto').off('click');
          button.show();
        }
      }
    });
    observer.observe(this.element, {
      attributes: true,
      attributeFilter: ['class']
    });

    $(this.element).find('.mdc-data-table__filter-textfield').on('input', (event: InputEvent) => {
      const index = $(this.element).index();
      const rows: Cash = $(this.element)
        .closest('table')
        .find('tbody tr');
      rows.hide();
      rows.filter(
        (index_, element) => $(element)
          .find(`td:nth-child(${index + 1})`)
          .text()
          .search(event.target.value) !== -1
      ).show();
    });
  }
}
