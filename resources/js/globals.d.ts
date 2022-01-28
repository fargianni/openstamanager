/* eslint-disable no-var,vars-on-top */
// noinspection ES6ConvertVarToLetConst

import {MWCCard} from '@maicol07/mwc-card';
import {
  LayoutGrid as MWCLayoutGrid,
  LayoutGridCell as MWCLayoutGridCell
} from '@maicol07/mwc-layout-grid';
import {Button as MWCButton} from '@material/mwc-button';
import {Checkbox as MWCCheckbox} from '@material/mwc-checkbox';
import {CircularProgress as MWCCircularProgress} from '@material/mwc-circular-progress';
import {Dialog as MWCDialog} from '@material/mwc-dialog';
import {Fab as MWCFab} from '@material/mwc-fab';
import {Formfield as MWCFormfield} from '@material/mwc-formfield';
import {IconButton as MWCIconButton} from '@material/mwc-icon-button';
import {IconButtonToggle as MWCIconButtonToggle} from '@material/mwc-icon-button-toggle';
import {LinearProgress as MWCLinearProgress} from '@material/mwc-linear-progress';
import {List as MWCList} from '@material/mwc-list';
import {ListItem as MWCListItem} from '@material/mwc-list/mwc-list-item.js';
import {Menu as MWCMenu} from '@material/mwc-menu';
import type cash from 'cash-dom';
import type Mithril from 'mithril';
import type router from 'ziggy-js';

import {
  JSXElement,
  OpenSTAManager
} from './typings';
import type {__ as translator} from './utils';
import MaterialDrawer from './WebComponents/MaterialDrawer';
import Select from './WebComponents/Select';
import TextArea from './WebComponents/TextArea';
import TextField from './WebComponents/TextField';
import TopAppBar from './WebComponents/TopAppBar';

declare global {
  let importPath: string;
  let translations: Record<string, string>;
  let modules: Record<string, OpenSTAManager.Modules>;
  const route: typeof router;

  var $: typeof cash;
  var m: typeof Mithril;
  // eslint-disable-next-line @typescript-eslint/naming-convention
  var __: typeof translator;

  namespace JSX {
    interface IntrinsicElements {
      'material-drawer': JSXElement<MaterialDrawer>;
      'material-select': JSXElement<Select>;
      'mwc-button': JSXElement<MWCButton & {dialogAction?: string | 'ok' | 'discard' | 'close' | 'cancel' | 'accept' | 'decline'}>;
      'mwc-checkbox': JSXElement<MWCCheckbox>;
      'mwc-card': JSXElement<MWCCard>
      'mwc-circular-progress': JSXElement<MWCCircularProgress>;
      'mwc-dialog': JSXElement<MWCDialog>;
      'mwc-fab': JSXElement<MWCFab>;
      'mwc-formfield': JSXElement<MWCFormfield>;
      'mwc-icon-button': JSXElement<MWCIconButton>;
      'mwc-icon-button-toggle': JSXElement<MWCIconButtonToggle>;
      'mwc-layout-grid': JSXElement<MWCLayoutGrid>;
      'mwc-layout-grid-cell': JSXElement<MWCLayoutGridCell> & {'span-desktop'?: number, 'span-tablet'?: number, 'span-phone'?: number};
      'mwc-linear-progress': JSXElement<MWCLinearProgress>;
      'mwc-list': JSXElement<MWCList>;
      'mwc-list-item': JSXElement<MWCListItem>;
      'mwc-menu': JSXElement<MWCMenu>;
      'text-area': JSXElement<TextArea>;
      'text-field': JSXElement<TextField>;
      'top-app-bar': JSXElement<TopAppBar>;
    }
  }
}
