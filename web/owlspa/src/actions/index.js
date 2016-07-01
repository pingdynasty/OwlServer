import fetch from 'isomorphic-fetch';
import { 
  API_END_POINT,
  REQUEST_PATCHES,
  RECEIVE_PATCHES,
  REQUEST_AUTHORS,
  RECEIVE_AUTHORS,
  REQUEST_TAGS,
  RECEIVE_TAGS,
  SET_PATCHLIST_TOP_FILTER,
  TOGGLE_FILTER_IN_SUB_FILTER,
  RESET_PATCHLIST_SUB_FILTER
} from 'constants';

export const fetchPatches = () => {
  return (dispatch) => {

    dispatch({
      type: REQUEST_PATCHES,
      isFetching: true
    });

    return fetch( API_END_POINT + '/patches/')
      .then(response => {
        return response.json();
      })
      .then( response => {
          if (response.status >= 400) {
            console.error('bad status:', response.status);
          } else {
            dispatch({
              type: RECEIVE_PATCHES,
              patches: response.result
            });
          }
        },
        (err) => {
          console.error(err);
        }
      );
  }
}

export const fetchAuthors = () => {
  return (dispatch) => {
    dispatch({
      type: REQUEST_AUTHORS,
      isFetching: true
    });

    return fetch( API_END_POINT + '/authors/')
      .then(response => {
        return response.json();
      })
      .then( response => {
          if (response.status >= 400) {
            console.error('bad status:', response.status);
          } else {
            dispatch({
              type: RECEIVE_AUTHORS,
              authors: response.result
            });
          }
        },
        (err) => {
          console.error(err);
        }
      );
  }
}

export const fetchTags = () => {
  return (dispatch) => {
    dispatch({
      type: REQUEST_TAGS,
      isFetching: true
    });

    return fetch( API_END_POINT + '/tags/')
      .then(response => {
        return response.json();
      })
      .then( response => {
          if (response.status >= 400) {
            console.error('bad status:', response.status);
          } else {
            dispatch({
              type: RECEIVE_TAGS,
              tags: response.result
            });
          }
        },
        (err) => {
          console.error(err);
        }
      );
  }
}

export const setPatchListTopFilter = (filter) => {
  return {
    type: SET_PATCHLIST_TOP_FILTER,
    topFilter: filter
  };
}

export const togglePatchListSubFilter = (subFilter) => {
  return {
    type: TOGGLE_FILTER_IN_SUB_FILTER,
    subFilter: subFilter
  };
}

export const resetPatchListSubFilter = () => {
  return {
    type: RESET_PATCHLIST_SUB_FILTER
  };
}
