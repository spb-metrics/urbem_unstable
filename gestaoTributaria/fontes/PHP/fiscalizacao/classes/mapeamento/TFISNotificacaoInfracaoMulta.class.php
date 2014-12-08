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
 * Classe de mapeamento para notificao_infracao_multa
 * Data de Criação: 01/09/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TFISNotificacaoInfracaoMulta.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

/**
 * Classe de mapeamento para notificacao_infracao_multa.
 */
class TFISNotificacaoInfracaoMulta extends Persistente
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.notificacao_infracao_multa' );

        $this->setCampoCod( 'cod_processo, cod_penalidade, cod_infracao' );
        $this->setComplementoChave( 'cod_infracao, cod_penalidade' );

        $this->addCampo( 'cod_processo', 'integer', true, '', true, true );
        $this->addCampo( 'cod_penalidade', 'integer', true, '', true, true );
        $this->addCampo( 'cod_infracao', 'integer', true, '', true, true );
        $this->addCampo( 'valor', 'numeric', true, '14,2', false, false );
        $this->addCampo( 'quantidade', 'numeric', true, '14,2', false, false );
    }
}
