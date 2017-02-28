import { CLIENT_ADD_PATCH_STAR } from 'constants';

const clientAddPatchStar = ({star, patchSeoName}) => {
  return {
    type: CLIENT_ADD_PATCH_STAR,
    star,
    patchSeoName
  }
}

export default clientAddPatchStar;