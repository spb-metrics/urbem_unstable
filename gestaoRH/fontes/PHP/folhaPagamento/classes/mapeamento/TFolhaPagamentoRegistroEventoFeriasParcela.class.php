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
    * Classe de mapeamento da tabela folhapagamento.registro_evento_ferias_parcela
    * Data de Criação: 21/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.53
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.registro_evento_ferias_parcela
  * Data de Criação: 21/06/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEventoFeriasParcela extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEventoFeriasParcela()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.registro_evento_ferias_parcela");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_registro,timestamp,cod_evento,desdobramento');

    $this->AddCampo('cod_registro'  ,'integer'  ,true   ,''  ,true    ,"TFolhaPagamentoUltimoRegistroEventoFerias");
    $this->AddCampo('timestamp'     ,'timestamp',false  ,''  ,true    ,"TFolhaPagamentoUltimoRegistroEventoFerias");
    $this->AddCampo('cod_evento'    ,'integer'  ,true   ,''  ,true    ,"TFolhaPagamentoUltimoRegistroEventoFerias");
    $this->AddCampo('desdobramento' ,'char'     ,true   ,'1' ,true    ,'TFolhaPagamentoUltimoRegistroEventoFerias');
    $this->AddCampo('parcela'       ,'integer'  ,true   ,'' ,false    ,false);

}
}
