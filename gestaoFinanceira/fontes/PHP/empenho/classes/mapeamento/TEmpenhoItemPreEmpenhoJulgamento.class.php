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
?>
<?php
/**
    * Classe de mapeamento da tabela EMPENHO.ITEM_PRE_EMPENHO_MAPA
    * Data de Criação: 18/01/2007

    * @author Analista: Lucas Teixeira Stephanou
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-15 15:55:02 -0200 (Qui, 15 Fev 2007) $

    * Casos de uso: uc-02.03.03, uc-02.03.02, uc-03.05.21
*/

/*
$Log$
Revision 1.1  2007/02/15 17:55:02  domluc
Mapeamento da Tabela empenho.item_pre_empenho_julgamento

Revision 1.2  2007/02/14 16:04:59  domluc
Alteração para corresponder ao ER

Revision 1.1  2007/01/18 19:19:28  domluc
Autorização de Licitação

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoItemPreEmpenhoJulgamento extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TEmpenhoItemPreEmpenhoJulgamento()
    {
        parent::Persistente();
        $this->setTabela('empenho.item_pre_empenho_julgamento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_pre_empenho,exercicio,num_item');

        $this->AddCampo('cod_pre_empenho','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'04',true,true);
        $this->AddCampo('num_item','integer',true,'',true,false);
        $this->AddCampo('cod_item','integer',true,'',true,false);
        $this->AddCampo('cod_cotacao','integer',true,'',false,true);
        $this->AddCampo('exercicio_julgamento','char',true,'4',false,true);
        $this->AddCampo('lote','integer',true,'',false,true);
        $this->AddCampo('cgm_fornecedor','integer',true,'',false,true);
    }
}
