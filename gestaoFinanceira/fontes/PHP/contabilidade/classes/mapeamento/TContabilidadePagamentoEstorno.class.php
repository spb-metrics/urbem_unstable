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
    * Classe de mapeamento da tabela contabilidade.pagamento_estorno
    * Data de Criação: 02/10/2007

    * @author Analista: Anderson cAko Konze
    * @author Desenvolvedor: Anderson cAko Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-10-03 16:12:51 -0300 (Qua, 03 Out 2007) $

    * Casos de uso: uc-02.02.04
*/
/*
$Log$
Revision 1.1  2007/10/03 19:08:47  cako
Ticket#9496#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadePagamentoEstorno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadePagamentoEstorno()
{
    parent::Persistente();
    $this->setTabela("contabilidade.pagamento_estorno");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,sequencia,tipo,cod_lote,cod_entidade');

    $this->AddCampo('exercicio','char',true,'04'    ,true,true);
    $this->AddCampo('sequencia','integer',true,''   ,true,true);
    $this->AddCampo('tipo','char',true,'1'          ,true,true);
    $this->AddCampo('cod_lote','integer',true,''    ,true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('cod_nota','integer',true,''    ,true,true);
    $this->AddCampo('timestamp','timestamp',true,'' ,true,true);
    $this->AddCampo('timestamp_anulada','timestamp',true,'',true,true);
    $this->AddCampo('exercicio_liquidacao','char',true,'04',true,true);

}
}
?>
