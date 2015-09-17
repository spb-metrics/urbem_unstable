<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/

/*
 * Classe de mapeamento da tabela tcmba.tipo_documento_pagamento
 *
 * @package URBEM
 * @subpackage Mapeamento
 * @version $Id: TTCMBATipoPagamento.class.php 63464 2015-08-31 17:30:39Z michel $
 * @author Michel Teixeira
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBATipoPagamento extends Persistente
{

    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.tipo_documento_pagamento');

        $this->setCampoCod('cod_tipo');

        $this->AddCampo('cod_tipo'  , 'integer', true , ''   , true, true);
        $this->AddCampo('descricao' , 'varchar', true , '35' , false, false);
    }
    
    public function __destruct() {}
}
?>